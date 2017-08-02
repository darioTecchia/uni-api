# uni-api
This PHP script allow you to create a simple and universal **REST API**. Based on the
original idea of Maurits van der Schee, 
[Creating a simple REST API in PHP](https://www.leaseweb.com/labs/2015/10/creating-a-simple-rest-api-in-php/ "Creating a simple REST API in PHP").

It use a MySQL Database.

uni-api support HTTP verbs **GET**, **POST**, **PATCH**, **PUT** and **DELETE**.

It have an authentication module.

# Table Of Contents
- [Install](#install)
- [Usage](#usage-&-api)
	- [user](#user)
	- [other tables](#other-tables)
        - [no relations](#no-relations)
        - [relations](#relations)

# Install

Save the file *api.php* in your server document root and configure the `config.php` file.

Once you have configured the `config.php` file with the right parameters, launch `install.php`.

Just launch it from the browser.

Check if there is any errors while the installation.

# Usage & API
The usage is realy simple:

## user

### Basic API

> **POST** ```/user``` <br>
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

--

> **PATCH** ```/user/:id``` <br>
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

> **GET** ```/table``` <br>
Get all the rows <br>
response:
```
[
  { 
    //row_one
  },
  { 
    //row_two
  }
]
```

--

> **GET** ```/table/:id``` <br>
Find row by ID <br>
response:
```
{ 
  //all table's fields
}
```

--

> **PUT** ```/table/:id``` <br>
Update a row in the database <br>
**Need to send the access_token in the Authorization header** <br>
request: 
```
{ 
    //row's fields i want to update
}
```
response:
```
{ 
    //updated row
}
```

--

> **POST** ```/table``` <br>
Create a new row <br>
**Need to send the access_token in the Authorization header** <br>
request: 
```
{ 
    //table's field without id and date_added
}
```
response:
```json
{ 
    //added row
}
```

### relations

#### hasOne

> **GET** ```/table/:id/relatedTable``` <br>
Get the element related to the table <br>
response:
```
  { 
    //all related table's fields
  }
```

--

> **DELETE** ```/table/:id/relatedTable``` <br>
Create a new row <br>
**Need to send the access_token in the Authorization header** <br>
request: 
```
{ 
    //table's field without id and date_added
}
```
response:
```json
{ 
    //added row
}
```