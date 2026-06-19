import aiomysql
from fastapi import APIRouter, HTTPException, Query

from database import get_main_db

router = APIRouter(prefix='/api', tags=['trips'])


@router.get('/trips')
async def list_trips(
    page: int = Query(1, ge=1),
    per_page: int = Query(10, ge=1, le=50),
):
    offset = (page - 1) * per_page
    conn = await get_main_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute('SELECT COUNT(*) AS total FROM trips')
            total = (await cur.fetchone())['total']
            await cur.execute(
                'SELECT id, name, destination, start_date, end_date, owner_id FROM trips ORDER BY id DESC LIMIT %s OFFSET %s',
                (per_page, offset),
            )
            rows = await cur.fetchall()
    finally:
        conn.close()
    return {
        'data': rows,
        'page': page,
        'per_page': per_page,
        'total': total,
        'last_page': (total + per_page - 1) // per_page if total else 1,
    }


@router.get('/trips/{trip_id}')
async def get_trip(trip_id: int):
    conn = await get_main_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT id, name, description, destination, start_date, end_date, owner_id FROM trips WHERE id=%s',
                (trip_id,),
            )
            trip = await cur.fetchone()
            if trip is None:
                raise HTTPException(status_code=404, detail='Поездка не найдена')
            await cur.execute('SELECT COUNT(*) AS c FROM tasks WHERE trip_id=%s', (trip_id,))
            trip['tasks_count'] = (await cur.fetchone())['c']
            await cur.execute('SELECT COUNT(*) AS c FROM tasks WHERE trip_id=%s AND is_done=1', (trip_id,))
            trip['done_count'] = (await cur.fetchone())['c']
    finally:
        conn.close()
    return trip


@router.get('/trips/{trip_id}/tasks')
async def trip_tasks(trip_id: int, status: str | None = Query(None, pattern='^(done|todo)$')):
    conn = await get_main_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute('SELECT id FROM trips WHERE id=%s', (trip_id,))
            if await cur.fetchone() is None:
                raise HTTPException(status_code=404, detail='Поездка не найдена')
            sql = 'SELECT id, title, is_done, trip_id FROM tasks WHERE trip_id=%s'
            params = [trip_id]
            if status == 'done':
                sql += ' AND is_done=1'
            elif status == 'todo':
                sql += ' AND is_done=0'
            sql += ' ORDER BY id'
            await cur.execute(sql, params)
            tasks = await cur.fetchall()
    finally:
        conn.close()
    return {'trip_id': trip_id, 'count': len(tasks), 'tasks': tasks}
