CREATE DATABASE IF NOT EXISTS tripping_main
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS tripping_api
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL ON tripping_main.* TO 'tripping'@'%';
GRANT ALL ON tripping_api.* TO 'tripping'@'%';
FLUSH PRIVILEGES;
