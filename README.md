# outsource me

This is the api for my web application www.outsource-me.pl (IN PROGRESS). The application was created as my engineering work "Project of an application supporting finding orders for programmers". The genesis of the work stems from the  experience and the observation of many threads on community groups in Poland about the lack of availability of a specific tool in the web application market, which would focus primarily on helping to find additional or short-term jobs for programmers.

# Functional requirements
- Ability to create an account in the application 
  - Selection of the type of account between the client and the programmer. 
  - An email should be sent to the email address with a specially generated URL to confirm the registration. 
  - Once the registration is confirmed, the user will receive an internal notification in the application and an email as a thank you.
- Ability to log in with registered user account and password.
- Ability to recover the password. 
  - A specially generated URL for changing the password is sent to the user's email address provided in the form.
- Ability to log in using a Google account.
- Viewing the About Us tab containing basic information about how the application works.
- Viewing of the Contact tab allowing contact with the application administrators.
- Viewing of job offers and programmer offers.
- Ability to filter bid results.
  - Ability to filter by technology.
  - Ability to filter by bid type (programmer/assignment).
  - Ability to filter by location.
  - Ability to filter by job type.
  - Ability to filter by experience.
- Ability to sort bid results.
  - Ability to sort by offer popularity.
  - Ability to sort by date of addition.
- After logging in at that time, possibility of displaying offers matched to the user's preferences.
  - A user logged in as a programmer/contractor, respectively, has the ability to have only the programmer's job/offers matched under his/her preferences, respectively.
  - Offers are tailored based on components such as: distance from the programmer/offer location, technologies included in the programmer/offer profile, programmer experience, programmer/contractor person reviews.
- Ability to search for results through the application's internal search engine.
  - Search for job listings.
  - Search for users.
- Ability to view profiles of other users.
- Ability to view own profile.
- Ability to edit one's own profile.
  - Editing personal information.
  - Changing the profile picture.
  - Editing the description.
  - Editing education.
  - Editing jobs.
  - Editing known technologies.
- Ability to view notifications.
  - Ability to remove a notification from the list.
- Ability to add a new offer.
- Ability to update an offer.
- Ability to automatically expire an offer after time.
  - Sending a notification internally in the application and to the email address with information about the expiration of the offer to the applicants and the principal.
- Ability to download the offer manually.
  - Sending an internal notification and an email with information about the expiration of the offer to the applicants.
- Ability to apply for an offer.
  - Based on the user's profile, the user's resume is generated and sent as an application for the offer.
  - Ability to add attachments and messages to the principal in the application form.
  - Sending an internal notification in the application and to the email address

## Use case diagram - in Polish
![img_1.png](img_1.png)

## Stack
- Docker
- PHP 8.2
- Symfony
- API Platform
- Redis
- Elasticsearch
- Mysql
- Varnish
- PHPUnit
- Easyadmin



Docker containers schema

![img.png](img.png)


