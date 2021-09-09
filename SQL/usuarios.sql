create user "teste"@"localhost" identified by "teste";
create user "teste"@"%" identified by "teste";
grant all on Computadores . * to "teste"@"localhost";
grant all on Computadores . * to "teste"@"%";
flush privileges;
