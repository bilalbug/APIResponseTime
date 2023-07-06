# Attendance Time Calculation(APIResponseBased)

This project integrates with a third-party API that provides user time and location data. It retrieves the data, processes it, and stores it in a database for further analysis and record-keeping.

## Installation

1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Run `composer install` to install the project dependencies.
4. Configure your database connection in the `.env` file.
5. Run `php artisan migrate` to create the necessary database tables.
6. (Optional) Run `php artisan db:seed` to seed the database with sample data.
7. Start the development server by running `php artisan serve`.

## Configuration

To configure the project, you need to set up the following:

- Third-Party API: Update the URL of the third-party API in the `APIResponseUserTimeLocation()` method of the `ApiResponseController` class.

- Office IP Addresses and Locations: Use the provided routes to manage office IP addresses and their corresponding locations. This information is used to determine whether a user is in the office or remote based on their IP address.

## Usage

1. Access the `/response` route to trigger the retrieval and processing of data from the third-party API.
2. The data will be processed and stored in the database.
3. Access the `/records/all` route to view the stored attendance records.

## File Structure

The project follows the standard Laravel file structure. Key files and directories include:

- `app/Http/Controllers/ApiResponseController.php`: Contains the controller responsible for handling API responses and processing data.
- `app/Models/OfficeIpAddress.php`: Represents the model for office IP addresses and their corresponding locations.
- `app/Models/EmployeesAttendanceRecord.php`: Represents the model for storing attendance records.
- `routes/api.php`: Defines the project's routes.

## Git

To view the Git commits in your terminal, use the `git log` command. This command displays a list of commits in reverse chronological order. You can use various options and filters with `git log` to customize the output.

You can clone this Git repository at https://github.com/httpbilal/APIResponseTime.git

## Docker

To run the project using Docker, follow these steps:

1. Build the Docker image by running `docker build -t attendance-app .` (replace `attendance-app` with your desired image name).
2. Start the Docker container with the following command: `docker run -p 8000:80 attendance-app` (replace `attendance-app` with your image name).
3. Access the application in your browser at `http://localhost:8000`.

Make sure you have Docker installed and running on your machine before executing these commands.

You can access the Docker repository of my existing image at https://hub.docker.com/r/bilalbug/attendance-app

## Contributing

Contributions to the project are welcome. If you encounter any issues or have suggestions for improvements, please submit an issue or pull request on the project's GitHub repository.

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Feel free to update and customize this `readme.md` file further with any additional information, specific project details, or sections that you find relevant to your project.
