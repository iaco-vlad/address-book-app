## 1. Backend setup
1. Go to the backend folder
```
cd backend
```
2. Create the Docker images
```
docker-compose up -d
```
3. Create the tables
```
docker exec -i address-book-mysql mysql -uaddress-book -pp@ss123 address-book < migrations/new/20240605_create_cities_and_address_book_tables.sql
```
```
docker exec -i address-book-mysql mysql -uaddress-book -pp@ss123 address-book < migrations/new/20240617_create_tags_and_groups_tables.sql
```
4. Run the seeder
```
docker exec -i address-book-mysql mysql -uaddress-book -pp@ss123 address-book < migrations/seeders/seeder.sql
```

## 2. Frontend setup
1. Go to the frontend folder
```
cd frontend
```
2. Start the frontend server
```
npm run dev
```