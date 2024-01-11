# Sử dụng image Node.js
FROM node:latest

# Tạo thư mục làm việc cho Laravel Echo Server
WORKDIR /var/www/laravel-echo-server

# Copy các file cần thiết vào thư mục làm việc
COPY . /var/www/laravel-echo-server

# Cài đặt Laravel Echo Server toàn cục
RUN npm install -g laravel-echo-server

# Lệnh khi khởi động container
CMD ["laravel-echo-server", "start", "--force"]
