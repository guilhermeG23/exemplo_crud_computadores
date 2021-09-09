create database if not exists Computadores;

use Computadores;

create table if not exists PCGerais (
	IDPC int not null primary key auto_increment,
	Serial varchar(44) not null,
	SO varchar(44) not null,
	Marca varchar(44) not null
);

create table if not exists Setores (
	IDSetor int not null primary key auto_increment,
	Setor varchar(44) not null
);

insert into Setores values (0, "RH");
insert into Setores values (0, "Financeiro");
insert into Setores values (0, "Contabilidade");
insert into Setores values (0, "Produção");

create table if not exists Relacionamento (
	PKPC int not null,
	PKSetor int not null,
	foreign key (PKPC) references PCGerais(IDPC),
	foreign key (PKSetor) references Setores(IDSetor),
	primary key(PKPC, PKSetor)
);

