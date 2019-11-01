User Model
==========

A basic user model with properties name, password and email. The first user with id=1 is the super-user who has all access rights. There is only one super-user. The rest are ordinary users with the same access rights. A user has to sign-up and then sign-in. The access control in implemented in presets. It uses PHP session.

# Properties

**name**

User name.

**password**

User password which will be kept as a hash.

**email**

User e-mail address. No varification is done on the e-mail.

**created_utc**

Record created timestamp in unix time.

**updated_utc**

Record updated timestamp in unix time.

# Actions

**sign_up**

Sign-up a new user. Pass in user name, password and e-mail address.

**sign_in**

Sign-in an existing user into a session.

**sign_out**

Sign-out current user from the session.

**read_own**

Read the signed-in user record. Return the user name, password, e-mail address and created timestamp.

**read**

Read a user record by name. Return the user name, password, e-mail address and created timestamp. *Super-user only*. 

**update_own**

Update the signed-in user record. Pass in password and e-mail address. User name cannot be changed.

**update**

Update a user record by name. Pass in password and e-mail address. User name cannot be changed. *Super-user only*.

**delete**

Delete a user record by name. Cannot delete the super-user. *Super-user only*.
