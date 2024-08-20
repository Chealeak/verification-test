# ROADMAP

## Introduction
This roadmap outlines the planned improvements and features for the Laravel API application that verifies user information from uploaded files.

## Current State
The application currently accepts file uploads, processes the data, and verifies user information. While the core functionality is in place, there are several opportunities for optimization and expansion.

## Roadmap Overview
- **Error Handling**: Improve error handling by using custom exceptions and Laravel’s built-in error reporting.
- **Caching**: Cache frequently used or computationally expensive verifications to reduce redundant processing.
- **Unit Tests**: Increase unit test coverage.
- **Detailed Reporting**: Generate detailed reports on the verification process, including success/failure rates and error logs.
- **Verification Statistics**: Implement an endpoint that provides statistics on the verification process, such as the number of files processed, success rates, and common validation errors.
- **User Guide**: Create a simple guide explaining how to use the API.
- **Developer Documentation**: Provide documentation on how the code is structured, how to set up a local development environment, and how to extend the application.
- **Monitoring and Logging**: Set up monitoring and logging to track application performance and errors in production.

## Requirement clarifications for further improvements
**The initial requirement "The API should return a 200 status code even if the file is not verified" is a bit ambiguous. Possible implementation options include:**:
  - The API returns a 200 status code for verified or not verified results, and the system stores them in the database only when the JSON file is valid and there are no other errors unrelated to the data verification process (e.g., 400, 500 status codes, etc.). If such errors occur, the API returns their respective status code and such verification requests will not be recorded to the database (**currently implemented**).
  - The API returns a 200 status code for verified, not verified results, or incorrect JSON file structure, and the system stores them in the database. If there are other errors, the API returns their respective status code and such verification requests will not be recorded to the database.
  - The API always returns a 200 status code regardless of the error, and the system stores every result in the database, whether successful or not.

**It is necessary to clarify with the client whether the requirement was understood correctly, and if not, then update the implementation.**
