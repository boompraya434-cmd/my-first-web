# ใช้ PHP 8.2 พร้อมเว็บเซิร์ฟเวอร์ Apache
FROM php:8.2-apache
# ติดตั้ง extension สำหรับเชื่อม MySQL ผ่าน MySQLi และ PDO
RUN docker-php-ext-install mysqli pdo_mysql
# นำ source code ไปไว้ใน document root ของ Apache
COPY ./src /var/www/html
# เปิดพอร์ต HTTP ของ Apache ภายใน container
EXPOSE 80