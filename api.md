3CX Configuration API Endpoint Specification
Authorization
Endpoint: Get Token
Departments
Endpoint: Check if Department Exists
Endpoint: Create a Department
Endpoint: Check if 3CX Live Chat URL Exists
Endpoint: Create 3CX Live Chat URL for a Department
Endpoint: Configure Department Call Routing
Endpoint: Delete a Department
Endpoint: Department Update
Users
Endpoint: Get List of Users
Endpoint: Check if User with the Same Email Exists on PBX
Endpoint: Create a User
Endpoint: Assign a Role to a User in a Department
Endpoint: Create a User-Friendly URL (Update User)
Endpoint: Create a User-Friendly URL - Validation Request
Endpoint: Delete Users in Batch
System Extensions
Endpoint: List Group Members
Endpoint: Get Default Group Properties
Endpoint: Create Shared Parking
Endpoint: List Group Members (To Retrieve Shared Parking Extensions)
Endpoint: Get Parking Details by Number
Endpoint: Delete Shared Parking by ID
Endpoint: Get 3CX Version
Authorization
Endpoint: Get Token
Description:
This endpoint implements authentication and grants access_token for appropriate roles.

Endpoint URL:
POST https://{{PBX_FQDN}}/connect/token

HTTP Method:
POST

Authentication Required:
Yes, Basic Authentication

Request Parameters

Body: URL-encoded parameters application/x-www-form-urlencoded
client_id (required, string): Fixed value, server_principal_id.
client_secret (required, string): Fixed value, sercret_key granted after setting Service Principal.
grant_type (required, string): Fixed value, client_credentials.
Response

Success Response:

Status Code: 200 OK
Content Type: application/json
Response Body Example:

{

  "token_type": "Bearer",

  "expires_in": 3600,

  "access_token": "ACCESS_TOKEN",

  "refresh_token": null

}


Error Responses:

401 Unauthorized
Content Type: application/json
Authentication failed due to invalid credentials.
Response Example:

{

  "error": "unauthorized",

  "error_description": "The request requires valid user authentication."

}


Notes

The access_token is valid for 3600 seconds; re-authentication is necessary upon expiration.

Departments
Endpoint: Check if Department Exists
Description:
This endpoint checks if a department with a specified name exists within the 3CX system. This is useful to verify uniqueness before creating a new department.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Groups?$filter=Name eq '3CX Test'

HTTP Method:
GET

Authentication Required:
Yes

Request Parameters

Query Parameter:
$filter (required, string): Filters results to check for the specific department name (e.g., Name eq '3CX Test').
Response

Success Response - No Department Found:

Status Code: 200 OK
Content Type: application/json; charset=utf-8
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups",

    "value": []

}


Success Response - Department Found:

Status Code: 200 OK
Content Type: application/json; charset=utf-8
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups",

    "value": [

        {

            "Name": "DEFAULT",

            "IsDefault": true,

            "HasMembers": true,

            "Number": "GRP0000",

            "Id": 28

        }

    ]

}


Error Responses

404 Not Found
The department with the specified name does not exist.
Notes
Use this endpoint to ensure that a department name is unique before attempting to create a new department in the 3CX system.

Endpoint: Create a Department
Description:
Creates a new department within the 3CX system with specified configurations, such as language, time zone, and various properties for managing call service options.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/Groups

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body: JSON object with department configurations.

{

    "AllowCallService": true,

    "Id": 0,

    "Language": "EN",

    "Name": "3CX Test",

    "PromptSet": "1e6ed594-af95-4bb4-af56-b957ac87d6d7",

    "Props": {

        "LiveChatMaxCount": 20,

        "PersonalContactsMaxCount": 500,

        "PromptsMaxCount": 10,

        "SystemNumberFrom": "300",

        "SystemNumberTo": "319",

        "TrunkNumberFrom": "340",

        "TrunkNumberTo": "345",

        "UserNumberFrom": "320",

        "UserNumberTo": "339"

    },

    "TimeZoneId": "51",

    "DisableCustomPrompt": true

}


Response

Success Response:

Status Code: 201 Created
Content Type: application/json; charset=utf-8
Location Header: Provides the URL of the newly created department.
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups/$entity",

    "Name": "3CX Test",

    "Id": 35,

    "Language": "EN",

    "Props": {

        "LiveChatMaxCount": 20,

        "PersonalContactsMaxCount": 500,

        "PromptsMaxCount": 10

    },

    "TimeZoneId": "51"

}


Error Responses

400 Bad Request
Content Type: application/json
If a department with the same name already exists.
Response Example:

{

    "error": {

        "message": "Name:\nWARNINGS.XAPI.DUPLICATE",

        "details": [

            {

                "target": "Name",

                "message": "WARNINGS.XAPI.DUPLICATE"

            }

        ]

    }

}


Notes

Ensure that the department name is unique before using this endpoint. If a department with the same name exists, the request will fail with a 400 status code.

Endpoint: Check if 3CX Live Chat URL Exists
Description:
This endpoint checks if a specified 3CX Live Chat URL (link) exists in the 3CX system, ensuring that a unique URL is used for each department's chat setup.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/WebsiteLinks?$filter=Link eq 'LiveChat123456'

HTTP Method:
GET

Authentication Required:
Yes

Request Parameters

Query Parameter:
$filter (required, string): Specifies the Live Chat URL to check (e.g., Link eq 'LiveChat123456').
Response

Success Response - URL Exists:

Status Code: 200 OK
Content Type: application/json; charset=utf-8
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#WebsiteLinks",

    "value": [

        {

            "Id": 1,

            "Link": "LiveChat123456",

            "ChatEnabled": true,

            "CallsEnabled": true,

            "General": {

                "Greeting": "DesktopAndMobile",

                "Authentication": "Both"

            },

            "Advanced": {

                "EnableDirectCall": true,

                "CommunicationOptions": "PhoneAndChat"

            }

        }

    ]

}


Success Response - URL Not Found:

Status Code: 200 OK
Content Type: application/json; charset=utf-8
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#WebsiteLinks",

    "value": [ ]

}


Error Responses

404 Not Found
Indicates the specified Live Chat URL does not exist.
Notes
This endpoint is useful to verify if a Live Chat URL is already in use, preventing duplication and ensuring unique URLs across the system.

Endpoint: Create 3CX Live Chat URL for a Department
Description:
This endpoint creates a new 3CX Live Chat URL associated with a specific department, allowing configuration of chat and call options, appearance, and interaction settings.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/WebsiteLinks

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body: JSON object containing chat configuration details

{

    "Advanced": {

        "CallTitle": "",

        "CommunicationOptions": "PhoneAndChat",

        "EnableDirectCall": true,

        "IgnoreQueueOwnership": false

    },

    "CallsEnabled": true,

    "ChatEnabled": true,

    "DefaultRecord": true,

    "DN": {

        "Id": 28,

        "Name": "DEFAULT",

        "Number": "GRP0000",

        "Type": "Group"

    },

    "General": {

        "AllowSoundNotifications": true,

        "Authentication": "None",

        "DisableOfflineMessages": false,

        "Greeting": "DesktopAndMobile"

    },

    "Group": "GRP0000",

    "Link": "3cxtest",

    "Name": "",

    "Styling": {

        "Animation": "NoAnimation",

        "Minimized": true

    },

    "Translations": {

        "GreetingMessage": "",

        "StartChatButtonText": "",

        "UnavailableMessage": ""

    },

    "Website": ["https://my.website.com"]

}


Response

Success Response

Status Code: 201 Created
Content Type: application/json; charset=utf-8
Location Header: Provides the URL of the newly created live chat.
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#WebsiteLinks/$entity",

    "Id": 2,

    "Group": "GRP0000",

    "Link": "3cxtest",

    "ChatEnabled": true,

    "CallsEnabled": true,

    "DefaultRecord": true

}


Error Responses

400 Bad Request
Content Type: application/json
Indicates that the specified Live Chat URL (Link) is already in use.
Response Example:

{

    "error": {

        "message": "Link:\nWARNINGS.XAPI.ALREADY_IN_USE",

        "details": [

            {

                "target": "Link",

                "message": "WARNINGS.XAPI.ALREADY_IN_USE"

            }

        ]

    }

}


Notes
This endpoint allows detailed customization for a department’s live chat, including visual styling, message translations, and interaction settings. Ensure the Link is unique to prevent duplication.

Endpoint: Configure Department Call Routing
Description:
This endpoint configures call routing rules for a specified department, allowing for different routing behaviors during office hours, breaks, holidays, and out-of-office periods.

Endpoint URL:
PATCH https://{{PBX_FQDN}}/xapi/v1/Groups(123)

HTTP Method:
PATCH

Authentication Required:
Yes

Request Parameters

Body: JSON object with call routing configurations.



{

    "Id": 123,

    "BreakRoute": {

        "IsPromptEnabled": false,

        "Route": {

            "External": "",

            "Number": "101",

            "To": "VoiceMail"

        }

    },

    "OfficeRoute": {

        "IsPromptEnabled": false,

        "Route": {

            "External": "",

            "Number": "101",

            "To": "Extension"

        }

    },

    "OutOfOfficeRoute": {

        "IsPromptEnabled": false,

        "Route": {

            "External": "",

            "Number": "101",

            "To": "VoiceMail"

        }

    },

    "HolidaysRoute": {

        "IsPromptEnabled": false,

        "Route": {

            "External": "",

            "Number": "101",

            "To": "VoiceMail"

        }

    }

}


Response

Success Response

Status Code: 204 No Content
Content Type: None
Error Responses

404 Not Found
Indicates that the department with the specified ID was not found.
Response Example:


{

    "error": "Department not found"

}


Notes

Use this endpoint to dynamically adjust call handling based on different scenarios, such as office hours and holiday schedules, for a specified department. Ensure that the department ID is correct to avoid the 404 Not Found error.

Endpoint: Delete a Department
Description:
This endpoint deletes a specified department from the 3CX system by its ID.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/Groups/Pbx.DeleteCompanyById

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body: JSON object with the department ID to delete.

{

    "id": 123

}


Response

Success Response

Status Code: 204 No Content
Content Type: None
Error Responses

404 Not Found
Indicates that the department with the specified ID does not exist.
Response Example:


{

    "error": "Department not found"

}


Notes

Ensure that the department ID is correct before attempting to delete, as this action cannot be undone once completed.

Endpoint: Department Update
Description:
This endpoint allows updating the details and properties of an existing department, including limits for live chat, prompts, system numbers, trunk numbers, and user numbers.

Endpoint URL:
PATCH https://{{PBX_FQDN}}/xapi/v1/Groups(123)

HTTP Method:
PATCH

Authentication Required:
Yes

Request Parameters

Body: JSON object with updated department properties.


{

    "Id": 123,

    "Name": "3CX Test1",

    "Props": {

        "LiveChatMaxCount": 20,

        "PersonalContactsMaxCount": 500,

        "PromptsMaxCount": 10,

        "SbcMaxCount": 20,

        "SystemNumberFrom": "300",

        "SystemNumberTo": "319",

        "TrunkNumberFrom": "340",

        "TrunkNumberTo": "345",

        "UserNumberFrom": "320",

        "UserNumberTo": "339"

    }

}


Response

Success Response

Status Code: 204 No Content
Content Type: None
Error Responses

404 Not Found
Indicates that the department with the specified ID does not exist.
Response Example:


{

    "error": "Department not found"

}


Notes

Use this endpoint to update specific department configurations. Ensure the department ID is correct to avoid the 404 Not Found error.

Users
Endpoint: Get List of Users
Description:
This endpoint retrieves a list of users, including details such as their ID, name, number, email address, and group memberships.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Users

HTTP Method:
GET

Authentication Required:
Yes

Request Parameters

Query Parameters:
$top: Limits the number of users returned (default set to 100).
$skip: Specifies the number of records to skip for pagination (default set to 0).
$orderby: Specifies the order in which records are returned (ordered by Number in this case).
$select: Specifies fields to retrieve. Options here include Id, FirstName, LastName, Number, and EmailAddress.
$expand: Expands related entities. Here, it expands Groups and their associated Rights.
Response

Success Response

Status Code: 200 OK
Content Type: application/json
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Users(Id,FirstName,LastName,Number,EmailAddress,Groups(Rights()))",

    "value": [

        {

            "FirstName": "FirstName12345",

            "LastName": "LastName",

            "EmailAddress": "xx@gmail.com",

            "Number": "100",

            "Id": 29,

            "Groups": [

                {

                    "GroupId": 28,

                    "Number": "100",

                    "MemberName": "LastName, FirstName12345",

                    "Name": "DEFAULT",

                    "Type": "Extension",

                    "CanDelete": true,

                    "Id": 6,

                    "Rights": {

                        "RoleName": "system_owners"

                    }

                }

            ]

        },

        {

            "FirstName": "sysadmin",

            "LastName": "",

            "EmailAddress": "",

            "Number": "101",

            "Id": 32,

            "Groups": [

                {

                    "GroupId": 28,

                    "Number": "101",

                    "MemberName": "sysadmin",

                    "Name": "DEFAULT",

                    "Type": "Extension",

                    "CanDelete": true,

                    "Id": 12,

                    "Rights": {

                        "RoleName": "users"

                    }

                }

            ]

        }

    ]

}


Error Responses

401 Unauthorized
Content Type: application/json
The request requires valid user authentication.
Notes
This endpoint provides user details along with group memberships and access rights. Ensure that pagination ($top and $skip) is handled as needed for large lists.

Endpoint: Check if User with the Same Email Exists on PBX
Description:
This endpoint checks if there is an existing user with the specified email address on the PBX system. It retrieves basic user details if a match is found.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Users

HTTP Method:
GET

Authentication Required:
Yes

Request Parameters

Query Parameters:
$top=1: Limits the search result to the first match, ensuring only one result is returned if found.
$filter: Filters by the email address in lowercase, e.g., tolower(EmailAddress) eq 'test@gmail.com'.
$orderby: Specifies the order in which records are returned (sorted by Number here).
$select: Specifies fields to retrieve. Options here include Id, FirstName, LastName, Number, and EmailAddress.
$expand: Expands related entities. Here, it expands Groups and their associated Rights.
Response

Success Responses

User Found
Status Code: 200 OK
Content Type: application/json
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Users(Id,FirstName,LastName,Number,EmailAddress,Groups(Rights()))",

    "value": [

        {

            "FirstName": "FirstName12345",

            "LastName": "LastName",

            "EmailAddress": "test@gmail.com",

            "Number": "100",

            "Id": 29,

            "Groups": [

                {

                    "GroupId": 28,

                    "Number": "100",

                    "MemberName": "LastName, FirstName12345",

                    "Name": "DEFAULT",

                    "Type": "Extension",

                    "CanDelete": true,

                    "Id": 6,

                    "Rights": {

                        "RoleName": "system_owners"

                    }

                }

            ]

        }

    ]

}


User Not Found

Status Code: 200 OK
Content Type: application/json
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Users(Id,FirstName,LastName,Number,EmailAddress,Groups(Rights()))",

    "value": []

}


Error Responses

401 Unauthorized
Content Type: application/json
The request requires valid user authentication.
Notes
Use this endpoint to verify if a user with a specific email already exists on the PBX. Ensure that the email is provided in lowercase for the filter to work correctly.

Endpoint: Create a User
Description:
This endpoint allows the creation of a new user on the PBX system, specifying key information such as name, email, password, and various settings.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/Users

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body Parameters (JSON):
AccessPassword: The user's access password (e.g., "QrzxYQwa5!").
EmailAddress: The email address of the user (e.g., "test@mail.com").
FirstName: The first name of the user (e.g., "TestFirstName").
Id: The user ID, typically set to 0 for new users.
Language: The preferred language (e.g., "EN").
LastName: The last name of the user (e.g., "TestLastName").
Number: The extension or user number (e.g., "211").
PromptSet: Identifier for the user's prompt set, as defined on the PBX (e.g., "1e6ed594-af95-4bb4-af56-b957ac87d6d7").
SendEmailMissedCalls: A boolean to send missed call notifications via email (e.g., true).
VMEmailOptions: Specifies voicemail email notifications, such as "Notification".
Require2FA: Boolean to require two-factor authentication (e.g., true).
Response

Success Responses

User Created
Status Code: 201 Created
Location Header: Contains the location of the newly created user resource, e.g., "https://PBX_FQDN:5001/xapi/v1/Users(38)"
Response Body Example:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Users/$entity",

    "Enable2FA": false,

    "Require2FA": true,

    "FirstName": "TestFirstName",

    "LastName": "TestLastName",

    "EmailAddress": "test@mail.com",

    "Number": "211",

    "Id": 38,

    "Language": "EN",

    "SendEmailMissedCalls": true,

    "VMEmailOptions": "Notification",

    "PromptSet": "1e6ed594-af95-4bb4-af56-b957ac87d6d7"

}


Error Responses

Number Already in Use
Status Code: 400 Bad Request
Content Type: application/json
Response Body Example:

{

    "error": {

        "code": "",

        "message": "Number:\nWARNINGS.XAPI.ALREADY_IN_USE",

        "details": [

            {

                "code": "",

                "target": "Number",

                "message": "WARNINGS.XAPI.ALREADY_IN_USE"

            }

        ]

    }

}


Notes

If a user with the specified Number already exists, the request will return a 400 Bad Request error indicating that the number is already in use.
Endpoint: Assign a Role to a User in a Department
Description:
This endpoint allows assigning a specified role to a user within a department by updating the user's group settings. Roles can be set for different permissions within a department.

Endpoint URL:
PATCH https://{{PBX_FQDN}}/xapi/v1/Users({{UserId}})

HTTP Method:
PATCH

Authentication Required:
Yes

Request Parameters

Path Parameters:
UserId: The unique identifier of the user (e.g., 120).
Body Parameters (JSON):
Groups: An array of group objects where each object contains:
GroupId: The department (group) ID to which the user is being assigned (e.g., 95).
Rights: Specifies the role assigned to the user within this department. Valid values for RoleName include:
"system_owners"
"system_admins"
"group_owners"
"managers"
"group_admins"
"receptionists"
"users"
Id: The ID of the user being updated (should match UserId in the URL).
Sample Request Body:


{

    "Groups": [

        {

            "GroupId": 95,

            "Rights": {

                "RoleName": "system_owners"

            }

        }

    ],

    "Id": 120

}


Response

Success Responses

Role Assigned Successfully
Status Code: 204 No Content
Response Body: None (indicates successful role assignment)
Error Responses

Common HTTP errors such as 404 Not Found if the specified user or department does not exist or 400 Bad Request for invalid role assignments or missing parameters.
Notes

Only one role can be assigned per department group for each user at a time.
Ensure that UserId in the path and Id in the body match.
Endpoint: Create a User-Friendly URL (Update User)
Description:
This endpoint updates a user's settings to enable the chat functionality and set a user-friendly URL (Click-to-Call ID) and Web Meeting friendly name.

Endpoint URL:
PATCH https://{{PBX_FQDN}}/xapi/v1/Users({{UserId}})

HTTP Method:
PATCH

Authentication Required:
Yes

Request Parameters

Path Parameters:
UserId: The unique identifier of the user (e.g., 123).
Body Parameters (JSON):
CallUsEnableChat: Boolean value to enable (true) or disable (false) chat for the user.
ClickToCallId: The user-friendly identifier for Click-to-Call, generally formatted with the user's name (e.g., "firstnamelastname2").
Id: The unique ID of the user (must match the UserId in the URL).
WebMeetingFriendlyName: A user-friendly name for Web Meeting, typically matching the ClickToCallId (e.g., "firstnamelastname2").
Sample Request Body:


{

    "CallUsEnableChat": true,

    "ClickToCallId": "firstnamelastname2",

    "Id": 123,

    "WebMeetingFriendlyName": "firstnamelastname2"

}


Response

Success Responses

Update Successful
Status Code: 204 No Content
Response Body: None (indicates the update was successful)
Error Responses

User Not Found
Status Code: 404 Not Found
Response Body: None
Description: Returned if the specified user ID does not exist in the system.
Notes

Ensure that UserId in the path and Id in the request body are identical for the request to be processed correctly.
This operation allows only specific fields to be updated; attempting to include additional fields may result in errors.
Endpoint: Create a User-Friendly URL - Validation Request
Description:
This endpoint is used to validate the availability of a user-friendly URL (Friendly Name) before assigning it. It checks if the chosen name and associated phone extension (Pair) are available and valid within the PBX system.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/WebsiteLinks/Pbx.ValidateLink

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body Parameters (JSON):
model: Contains details for URL validation.
FriendlyName: The desired user-friendly URL name to validate (e.g., "test").
Pair: The associated phone extension number (e.g., "100").
Sample Request Body:


{

    "model": {

        "FriendlyName": "test",

        "Pair": "100"

    }

}


Response

Success Responses

Validation Successful
Status Code: 204 No Content
Response Body: None (indicates that the name is available and valid)
Error Responses

Name Already in Use or Invalid Pair
Status Code: Various error codes may apply depending on the reason for failure.
Response Body: None
Description: The friendly name or pairing with the extension number may already be in use or invalid.
Notes

This endpoint does not create the URL but only checks its availability.
After a successful validation (204 response), you may proceed with the URL creation in a subsequent step.
Endpoint: Delete Users in Batch
Description:
This endpoint is used to delete multiple users from the PBX system by specifying their user IDs in a batch request.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/Users/Pbx.BatchDelete

HTTP Method:
POST

Authentication Required:
Yes

Request Parameters

Body Parameters (JSON):
Ids: An array of user IDs to be deleted.
Example: {"Ids": [37, 38]}
Sample Request Body:


{

    "Ids": [

        37,

        38

    ]

}


Response

Success Responses

Users Deleted
Status Code: 200 OK
Response Body: JSON object confirming deletion or any encountered errors.
Example Response:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Collection(Pbx.UserDeleteError)",

    "value": []

}


Error Responses

User Not Found or Other Errors
Status Code: Dependent on error context
Response Body: Error information for any issues encountered during deletion.
Notes

If no issues are encountered, the value field in the response will be an empty array, confirming the users were successfully deleted.
Errors, if any, will be listed within the value array, each specifying details on why a user could not be deleted.
System Extensions
Endpoint: List Group Members
Description:
This endpoint retrieves the details of a specified group, including its members.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Groups({GroupId})?$expand=Members

HTTP Method:
GET

Authentication Required:
Yes

Path Parameters

GroupId: The ID of the group for which you want to retrieve member details.
Query Parameters

$expand=Members: Expands the result to include member details.
Sample Request URL


https://{{PBX_FQDN}}/xapi/v1/Groups(95)?$expand=Members


Response

Success Response

Status Code: 200 OK
Response Body: JSON object with details of the group, including member information.
Example Response:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups(Members())/$entity",

    "Name": "DEFAULT",

    "IsDefault": true,

    "HasMembers": true,

    "Members": [

        {

            "GroupId": 95,

            "Number": "101",

            "MemberName": "sysadmin",

            "Name": "DEFAULT",

            "Type": "Extension",

            "CanDelete": true,

            "Id": 56

        },

        {

            "GroupId": 95,

            "Number": "SP0",

            "MemberName": "Shared parking",

            "Name": "DEFAULT",

            "Type": "Parking",

            "CanDelete": true,

            "Id": 40

        }

    ]

}


Notes

This endpoint expands the group data to show the list of members, detailing each member's ID, number, type, and name.
Additional information such as group routing configurations and settings (e.g., OfficeRoute, BreakRoute, OutOfOfficeRoute, HolidaysRoute) may also be included in the response.
Endpoint: Get Default Group Properties
Description:
This endpoint retrieves the properties of the group named "DEFAULT," allowing you to view specific configuration details for this group.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Groups?$filter=Name eq 'DEFAULT'

HTTP Method:
GET

Authentication Required:
Yes

Query Parameters

$filter=Name eq 'DEFAULT': Filters the results to only retrieve the group with the name "DEFAULT."
Sample Request URL


https://{{PBX_FQDN}}/xapi/v1/Groups?$filter=Name eq 'DEFAULT'


Response

Success Response

Status Code: 200 OK
Response Body: JSON object with properties of the "DEFAULT" group.
Example Response:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups",

    "value": [

        {

            "Name": "DEFAULT",

            "IsDefault": true,

            "HasMembers": true,

            "AnswerAfter": 0,

            "AllowCallService": true,

            "PromptSet": "",

            "GloballyVisible": false,

            "Number": "GRP0000",

            "Id": 95,

            "Props": {

                "StartupLicense": "Pro",

                "LiveChatMaxCount": -1,

                "DectMaxCount": -1,

                "SbcMaxCount": -1,

                "PersonalContactsMaxCount": -1,

                "PromptsMaxCount": -1

            },

            "OfficeRoute": {

                "Prompt": "",

                "Route": {

                    "Number": "101",

                    "To": "Extension",

                    "Name": "sysadmin"

                }

            },

            "OutOfOfficeRoute": {

                "Prompt": "",

                "Route": {

                    "Number": "101",

                    "To": "VoiceMail",

                    "Name": "sysadmin"

                }

            },

            "BreakRoute": {

                "Prompt": "",

                "Route": {

                    "Number": "101",

                    "To": "VoiceMail",

                    "Name": "sysadmin"

                }

            },

            "HolidaysRoute": {

                "Prompt": "",

                "Route": {

                    "Number": "101",

                    "To": "VoiceMail",

                    "Name": "sysadmin"

                }

            }

        }

    ]

}


Notes

The response includes settings for various call routes (e.g., OfficeRoute, BreakRoute, OutOfOfficeRoute, HolidaysRoute).
You can see limits and license details in Props, including counts for features such as LiveChatMaxCount and PromptsMaxCount.
Endpoint: Create Shared Parking
Description:
This endpoint allows you to create a shared parking extension, associating it with multiple groups.

Endpoint URL:
POST https://{{PBX_FQDN}}/xapi/v1/Parkings

HTTP Method:
POST

Authentication Required:
Yes

Request Body

Groups: An array specifying the groups the parking should be associated with. Each entry includes:
GroupId: ID of the group (e.g., "122" for a custom group and "95" for the DEFAULT group).
Id: Set to 0 to create a new parking entry.
Sample Request Body


{

    "Groups": [

        {

            "GroupId": 122

        },

        {

            "GroupId": 95

        }

    ],

    "Id": 0

}


Success Response

Status Code: 201 Created
Response Body: JSON object confirming the shared parking entry was created.
Example Response:

{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Parkings/$entity",

    "Number": "SP11",

    "Id": 126

}


Notes

Location Header: Contains the URL to access the new shared parking object.
The shared parking Number (e.g., "SP11") and Id are provided upon successful creation.
Endpoint: List Group Members (To Retrieve Shared Parking Extensions)
Description:
This endpoint retrieves the members of a specified group, such as "DEFAULT," including any shared parking extensions within the group.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Groups(95)?$expand=Members

HTTP Method:
GET

Authentication Required:
Yes

Query Parameters

$expand: Set to "Members" to include the members of the group in the response.
Request Example


GET https://{{PBX_FQDN}}/xapi/v1/Groups(95)?$expand=Members


Success Response

Status Code: 200 OK
Response Body: JSON object listing all members of the specified group, including details for shared parking extensions and other members.
Example Response:


{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Groups(Members())/$entity",

    "Name": "DEFAULT",

    "Id": 95,

    "Members": [

        {

            "GroupId": 95,

            "Number": "SP11",

            "MemberName": "Shared parking",

            "Type": "Parking",

            "CanDelete": true,

            "Id": 59

        },

        {

            "GroupId": 95,

            "Number": "SP0",

            "MemberName": "Shared parking",

            "Type": "Parking",

            "CanDelete": true,

            "Id": 40

        },

        {

            "GroupId": 95,

            "Number": "101",

            "MemberName": "sysadmin",

            "Type": "Extension",

            "CanDelete": true,

            "Id": 56

        }

    ]

}


Notes

Use this endpoint to identify shared parking IDs (e.g., "SP11") that need to be deleted from the group.
Each shared parking extension includes the property "CanDelete": true, indicating it can be removed if needed.
Endpoint: Get Parking Details by Number
Description:
This endpoint retrieves detailed information about a specific parking spot by its number, such as "SP11". This data is useful for confirming the parking ID and associated details before deletion.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Parkings/Pbx.GetByNumber(number='SP11')

HTTP Method:
GET

Authentication Required:
Yes

Request Example

[multcode]

GET https://{{PBX_FQDN}}/xapi/v1/Parkings/Pbx.GetByNumber(number='SP11')


Success Response

Status Code: 200 OK
Response Body: JSON object with parking details, including the parking number and ID.
Example Response:


{

    "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Parkings/$entity",

    "Number": "SP11",

    "Id": 126

}


Notes

This endpoint provides the "Id" of the parking slot, essential for deletion in subsequent steps.
Ensure the correct parking number is used in the request to avoid accidental deletions.
Endpoint: Delete Shared Parking by ID
Description:
This endpoint is used to delete a specific shared parking spot in the 3CX system by its unique ID, such as "126".

Endpoint URL:
DELETE https://{{PBX_FQDN}}/xapi/v1/Parkings(126)

HTTP Method:
DELETE

Authentication Required:
Yes

Request Example


DELETE https://{{PBX_FQDN}}/xapi/v1/Parkings(126)


Expected Responses

Successful Deletion
Status Code: 204 No Content
Response Body: None
Deletion Failure (Parking Not Found)
Status Code: 404 Not Found
Response Body: None
Explanation: This status indicates that the specified parking ID does not exist in the system.
Example Scenarios:

Successful Deletion
The specified parking ID, 126, exists and is deleted successfully.
Failure (Not Found)
If the specified parking ID, such as 123, does not exist, a 404 response will be returned, indicating the parking was not found.
Notes

Verify the parking ID before deletion to avoid errors.
Deletion is permanent; ensure no active dependencies on the shared parking slot before proceeding.
Endpoint: Get 3CX Version
Description:
This endpoint is primarily used to verify successful authentication and to retrieve the 3CX version by querying a basic endpoint.

Endpoint URL:
GET https://{{PBX_FQDN}}/xapi/v1/Defs?$select=Id

HTTP Method:
GET

Authentication Required:
Yes

Request Example


GET https://{{PBX_FQDN}}/xapi/v1/Defs?$select=Id


Expected Responses

Successful Authentication and Version Retrieval
Status Code: 200 OK
Response Headers:
X-3CX-Version: Contains the version of the 3CX system, e.g., "20.0.3.6"
Response Body:

{

  "@odata.context": "https://PBX_FQDN/xapi/v1/$metadata#Defs(Id)",

  "Id": 0

}


Response Headers Sample

X-3CX-Version: 20.0.3.6 — this header indicates the current version of 3CX in use.
Example Scenarios:

Successful Response
The endpoint confirms authentication and retrieves the version info if authentication is successful.

Notes

The X-3CX-Version header is a convenient way to confirm the 3CX version directly from the response headers.
This endpoint is generally used as a quick connectivity test, verifying successful access to the 3CX API.