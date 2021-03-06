# uni-api
This PHP script allow you to create a simple and universal **REST API**. Based on the
original idea of Maurits van der Schee, 
[Creating a simple REST API in PHP](https://www.leaseweb.com/labs/2015/10/creating-a-simple-rest-api-in-php/ "Creating a simple REST API in PHP").

It use a MySQL Database.

uni-api support HTTP verbs **GET**, **POST**, **PATCH**, **PUT** and **DELETE**.

It have an authentication module.

# Table Of Contents
- [Install](#install)
- [Configure the models](#configure-the-models)
- [Usage](#usage--api)
	- [user](#user)
	- [other tables](#other-tables)
        - [no relations](#hasone)
        - [relations](#hasmany)
- [WIP](#wip)

# Install

- Save the file *api.php* and */core* folder in your server document root
 
- configure the `config.php` file with the requested parameters

- Configure the database's models in `models.json`
    - the main use it will be automatically created with *admin* as username and password 

- launch `install.php` from the browser

- Check if there is any errors while the installation

# Configure the models
There is a models already written by me in the project folder.

# Usage & API
The usage is realy simple:

## user

### Basic API

> **POST** api.php```/user``` <br>
Logs user into the system <br>

request: 
```json
{ 
    "username": "foo",
    "password": "foopass"
}
```
response:
```json
{ 
    "username": "foo",
    "id": "foo_id",
    "token": "access_token"
}
```


> **PATCH** api.php```/user/:id``` <br>
Update user <br>

request: (you can send just username, password or both)
```json
{ 
    "username": "new_foo_username",
    "password": "new_foo_pass"
}
```
response:
```json
{ 
    "count": "# of the rows affected"
}
```

## other tables

### no relations

> **GET** api.php```/table``` <br>
Get all the rows <br>

response:
```json
[
  { 
    //row_one
  },
  { 
    //row_two
  }
]
```



> **GET** api.php```/table/:id``` <br>
Find row by ID <br>

response:
```json
{
    //all table's fields
}
```



> **PUT** api.php```/table/:id``` <br>
Update a row in the database <br>
**Need to send the access_token in the Authorization header** <br>

request: 
```json
{ 
    //row's fields i want to update
}
```
response:
```json
{ 
    "count": "# of the rows affected"
}
```



> **POST** api.php```/table``` <br>
Create a new row <br>
**Need to send the access_token in the Authorization header** <br>

request: 
```json
{ 
    //table's field without id and update_date
}
```
response:
```json
{ 
    "count": "# of the rows affected"
}
```


> **DELETE** api.php```/table/:id``` <br>
Create a new row <br>
**Need to send the access_token in the Authorization header** <br>

response:
```json
{ 
    "count": "# of the rows affected"
}
```

### relations

#### hasOne

> **GET** api.php```/table/:id/relatedTable``` <br>
Delete <br>

- Make a **GET** request to the table;
- Get the relatedTable_id from the body;
- Make a **GET** request to the relatedTeble with the obtained id.

> **PUT** api.php```/table/:id/relatedTable``` <br>
Create a new relation <br>
**Need to send the access_token in the Authorization header** <br>

- Just update the relatedTable_id field with the related element's id.

> **DELETE** api.php```/table/:id/relatedTable``` <br>
Create a new row <br>
**Need to send the access_token in the Authorization header** <br>

- Just set to **NULL** the relatedTable_id field

#### hasMany

> **GET** api.php```/table/:id/relatedTable``` <br>
Get the realted elements of a determinate table <br>
**Need to send the access_token in the Authorization header** <br>

response:
```json
{ 
    [
      { 
        //row_one
      },
      { 
        //row_two
      }
    ]
}
```


> **POST** api.php```/table/:id/relatedTable``` <br>
Relate an existent element to the table <br>
**Need to send the access_token in the Authorization header** <br>

request: 
```json
{ 
    // an existent element to relate
}
```
response:
```json
{ 
    "count": "# of the rows affected"
}
```


> **DELETE** api.php```/table/:id/relatedTable/:relatedId``` <br>
Delete a relation between two elements <br>
**Need to send the access_token in the Authorization header** <br>

response:
```json
{ 
    "count": "# of the rows affected"
}
```

# WIP

- **User**
    - [ ] User permission
    - [ ] Custom user
    - [ ] Better Multi User support
- **Files**
    - [ ] Files support
- **Models**
    - [ ] At State of Art you can't delete models or update it after. I want to make that
    possible.
    - [ ] Better configuration of the models. (I.E.: Required/Not Required fields)
- **Example**
    - [ ] Write a complete example.
