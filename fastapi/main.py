from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from routers import trips, ws

app = FastAPI(title='TripPing API', version='1.0.0')

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
