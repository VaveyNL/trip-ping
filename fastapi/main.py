from contextlib import asynccontextmanager
import asyncio
import json
import os

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import redis.asyncio as aioredis

from routers import trips, ws

REDIS_URL = os.environ.get(
    'REDIS_URL',
    'redis://' + os.environ.get('REDIS_HOST', '127.0.0.1') + ':6379',
)


async def redis_subscriber():
    # Слушает Redis и пробрасывает события задач всем по WebSocket.
    r = aioredis.from_url(REDIS_URL)
    pubsub = r.pubsub()
    await pubsub.subscribe('task.added', 'task.toggled', 'task.deleted')
    print('[redis] subscriber запущен: task.added, task.toggled, task.deleted', flush=True)

    async for message in pubsub.listen():
        if message['type'] != 'message':
            continue
        channel = message['channel'].decode()
        data = json.loads(message['data'])

        payload = {'event': channel}
        payload.update(data)
        await ws.manager.broadcast(payload)


@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()


app = FastAPI(title='TripPing API', version='1.0.0', lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=['http://localhost'],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)

app.include_router(trips.router)
app.include_router(ws.router)


@app.get('/api/health')
async def health():
    return {'ok': True}
