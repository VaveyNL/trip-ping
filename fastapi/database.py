import os
import aiomysql

DB_CONFIG = {
    'host':     os.environ.get('DB_HOST', '127.0.0.1'),
    'port':     int(os.environ.get('DB_PORT', 3306)),
    'user':     os.environ.get('DB_USER', 'tripping'),
    'password': os.environ.get('DB_PASSWORD', 'tripping_pass'),
    'db':       os.environ.get('DB_NAME', 'tripping_api'),
    'charset':  'utf8mb4',
}

# База Laravel (поездки, задачи). FastAPI только читает её
MAIN_DB_NAME = os.environ.get('MAIN_DB_NAME', 'tripping_main')


async def get_db():
    # Своя база FastAPI (tripping_api) - для чата
    return await aiomysql.connect(
        host=DB_CONFIG['host'],
        port=DB_CONFIG['port'],
        user=DB_CONFIG['user'],
        password=DB_CONFIG['password'],
        db=DB_CONFIG['db'],
        charset=DB_CONFIG['charset'],
    )


async def get_main_db():
    # База Laravel (tripping_main) - только чтение поездок и задач
    return await aiomysql.connect(
        host=DB_CONFIG['host'],
        port=DB_CONFIG['port'],
        user=DB_CONFIG['user'],
        password=DB_CONFIG['password'],
        db=MAIN_DB_NAME,
        charset=DB_CONFIG['charset'],
    )
