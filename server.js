const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const session = require('express-session');
const bcrypt = require('bcrypt');
const User = require('./models/user');
const app = express();

// fix
const username = 'Jeremy';
const password = 'lepotonpenis25@88889';
const host = '127.0.0.1';
const port = '53930';
const database = 'Hackathon';

const actualConnectionString = `mongodb://${username}:${password}@${host}:${port}/${database}`;

const mongoURI = 'mongodb://<username>:<password>@<host>:<port>/<database>';
mongoose.connect('mongodb://localhost/myapp', { useNewUrlParser: true });

app.use(bodyParser.urlencoded({ extended: true }));

//add secret key
app.use(session({ secret: 'your-secret-key', resave: true, saveUninitialized: true }));


//add route path
app.use('/auth', require('./routes/auth'));

app.listen(3000, () => {
  console.log('Server is running on port 3000');
});
