### Event Booking Code Test

## Features

1. User can login as existing user, username: admin, password: admin
2. We also provide an option to register new users
3. Once logged in, user has the following sections
    # Dashboard
        - Sales report from a selected date is shown in a graph
        - It allows to filter the report graph with different number of days
        - It shows the total sales amount for the selected time period
    # Events
        - Users can add new events, capacities and their prices per head
        - Users can see the list of events and delete them
    # Bookings
        - Users can add new bookings for any selected event along with count
        - Users can see the list of bookings and delete them
        - It'll show the event dates while adding a booking
        - And it shows the number of seats left in each type of slot
        - Auto calculate the total amount based on the type amount and count
    # Reports
        - Users can see the booking reports and filter by the events
4. We add initial sample data for all the tables for the ease of testing
5. The users are allowed to see only their data
6. The authentication and authorization were managed in the application 

## Requirements
    - Make sure you have php-xml, php-gd, php-mysql and php-mbstring enabled with your PHP configuration

:warning: Please do not miss any steps below while you setup the project

## Steps to setup the project

# To run the API

1. $ cd /path/to/your/www/
2. $ git clone https://github.com/rafeequekhp134/event-management-ticketing.git
3. Create a MySQL database with the name 'ticketing_app'.
4. Copy the .env.example to .env and edit the configs if you have any change in database host, username or password.
5. $ cd event-management-ticketing
6. Create the folder /database/factories if not exists
7. $ composer install
8. $ npm install
9. $ php artisan key:generate
10. $ php artisan jwt:secret
11. $ php artisan migrate
12. $ php artisan db:seed
13. $ php artisan serve

# To run the UI

1. Open to another terminal window in the project root
2. $ npm run watch
3. Then go to the link http://127.0.0.1:8000 in your browser.
4. Register the user and login to get into the dashboard
5. Test username: admin, password: admin
