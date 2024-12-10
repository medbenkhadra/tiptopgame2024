### TipTop
## 1 default profiles
# ADMIN:
      email : eric.bourdon@gmail.com
      mdp : Mohamed6759F@


# ADMIN 2:
    +  email : admin@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# STORE MANAGER:
    +  email : manager@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# EMPLOYEE:
    +  email : employee@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# CLIENT:
    +  email : client@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# BAILIFF:
    +  email :  bailiff@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# BAILIFF2:
    +  email :  rick.arnaud@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@



# Tiptop game project
# Getting Started
## Backend setup

### Symfony
#### Migration and fixtures


### - Go to the backend directory
```bash
cd backend
```

### 1- Install dependencies
```bash
composer install
```

2- Create database
```bash
php bin/console d:d:c
```

3- Run migrations
```bash
php bin/console d:m:m
```

4- Load fixtures
```bash
php bin/console d:f:l
```





### Reset game and generate all data in one command 

-- This command will purge all tables and generate all data from scratch

-- data generated Tables:
+ game configuration
+ role
+ prize
+ user
+ avatar
+ store
+ user_store
+ user_personal_info
+ loyalty_points
+ badge
+ user_badge
+ ticket_history
+ connection_history
+ emailing_history
+ action_history
+ email_service
+ email_template
+ email_template_variable

-- data generated :
+ 6 roles (ROLE_ADMIN, ROLE_STOREMANAGER, ROLE_EMPLOYEE, ROLE_CLIENT , ROLE_BAILIFF , ROLE_ANONYMOUS)



+ 5 prizes 
  + Infuser
  + Tea Box (100g) 
  + Signature Tea Box (100g) 
  + Discovery box (Value: 39€)
  + Discovery box (Value: 69€)

+ 5 Badges
    + Explorateur des Saveurs - Niveau 1
    + Maître Infuseur - Niveau 2
    + Collectionneur de Thé - Niveau 3
    + Gourmet du Thé - Niveau 4
    + Grand Maître du Thé - Niveau 5

+ Tickets codes generated for the wheel of fortune 
  + ***Customize the number of tickets wanted in src/Command/GenerateTicketsCommand.php***
  + by default 1000 tickets will be generated

+ Fake data generated
    + 5 stores
    + 5 managers (store managers)
    + 20 employees (caissiers)
    + 30 clients
    + 50 tickets history

+ Game configuration (period of the game)


#### **- This may take a long time just wait for the end of the process please 😀**

```bash
php bin/console app:reset-game 
```

## Output exemple
```bash

Purging table ticket_history
Purging table user_badge
Purging table store_user
Purging table user_store
Purging table user_personal_info
Purging table user
Purging table store
Purging table loyalty_points
Purging table connection_history
Purging table emailing_history
Purging table action_history
Purging table avatar
Next  Generate Role...
Default roles created successfully. 1/10
Loading...
Next  Generate Company and admin profile...
Default company created successfully. 2/10
Loading...
Next  Generate Prizes...
Prizes created successfully. 3/10
Loading...
Next  Generate Game Config...
Badges generated successfully. 4/10
Loading...
Next  Generate Badges...
Badges generated successfully. 5/10
Loading...
Next  Generate Tickets...
Tickets generated successfully. 6/10
Loading...
Next  Generate Email Services...
Email Services generated successfully. 7/10
Loading...
Next  Generate Email Templates Variables...
Email Templates Variables generated successfully. 8/10
Loading...
Next  Generate Email Templates...
Email Templates generated successfully. 9/10
Loading...
Next  Generate Fake Data...
Data generated successfully. 10/10
100% Complete
Game reset successfully.

```

### 5- Run the server
```bash
symfony serve
```

### 6- PhpUnit tests
```bash
  composer test
```

#### With coverage report
```bash
  composer test:coverage
```






Next.js
## Frontend setup

### - Go to the frontend directory
```bash
cd frontend
```

### 1- Install dependencies
```bash
npm install
```

#### Test the frontend
```bash
npm test
```



### 2- Run the server
```bash
npm run dev
```

### 3- Open your browser and go to http://localhost:3000



## Use those profiles to test the game
# 5 STORE MANAGERS:
    +  $i = 1,2,3,4,5
    +  email : manager${i}@dsp5-archi-f23-15m-g7.fr
    +  mdp : Mohamed6759F@

# 15 EMPLOYEES:
    +  $i = 1-15
    +  email : employee${i}@dsp5-archi-f24-15m-g7.fr
    +  mdp : Mohamed6759F@

# 30 CLIENTS:
    +  $i = 1-30
    +  email : client${i}@dsp5-archi-f24-15m-g7.fr
    +  mdp : Mohamed6759F@

#
#### Or you can use the tiptop profiles created in the top of readme.md file



### 4- Enjoy the game 🎉🎉🎉






