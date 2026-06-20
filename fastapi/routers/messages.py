import aiomysql
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from database import get_db
from routers.ws import manager

router = APIRouter(prefix='/api', tags=['messages'])


class MessageIn(BaseModel):
    user_name: str
    body: str


@router.get('/trips/{trip_id}/messages')
async def list_messages(trip_id: int):
    # Чтение истории сообщений поездки из tripping_api
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'SELECT id, trip_id, user_name, body, created_at FROM messages WHERE trip_id=%s ORDER BY id',
                (trip_id,),
            )
            rows = await cur.fetchall()
    finally:
        conn.close()
    return {'trip_id': trip_id, 'count': len(rows), 'messages': rows}


@router.post('/trips/{trip_id}/messages', status_code=201)
async def create_message(trip_id: int, payload: MessageIn):
    # МУТИРУЮЩИЙ эндпоинт: FastAPI пишет сообщение в свою базу
    body = payload.body.strip()
    if not body:
        raise HTTPException(status_code=422, detail='Пустое сообщение')

    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(
                'INSERT INTO messages (trip_id, user_name, body) VALUES (%s, %s, %s)',
                (trip_id, payload.user_name, body),
            )
            await conn.commit()
            new_id = cur.lastrowid
            await cur.execute(
                'SELECT id, trip_id, user_name, body, created_at FROM messages WHERE id=%s',
                (new_id,),
            )
            message = await cur.fetchone()
    finally:
        conn.close()

    # Рассылаем новое сообщение всем по WebSocket (без created_at, чтобы json.dumps не споткнулся)
    await manager.broadcast({
        'event': 'message.created',
        'trip_id': trip_id,
        'message': {
            'id': message['id'],
            'user_name': message['user_name'],
            'body': message['body'],
        },
    })

    return message
