-- Accede a la BD vacunacion, con usuario y contraseña "sanitario".
USE vacunacion;

-- Crea tabla usuarios
CREATE TABLE usuarios (
                          dni VARCHAR(9) PRIMARY KEY,
                          ss INT(10) NOT NULL UNIQUE,
                          apellido VARCHAR(255) NOT NULL,
                          fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
                          hospital VARCHAR(255),
                          tipo_vacuna VARCHAR(255),
                          brazo_dom VARCHAR(255),
                          antecedentes VARCHAR(255),
                          riesgo VARCHAR(255),
                          fecha_vacunacion DATETIME
);

-- Añadir datos de prueba
INSERT INTO usuarios VALUES ('11111111A', '111111111', 'Perez', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('22222222A', '222222222', 'Gonzalez', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('33333333A', '333333333', 'Cristobal', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('44444444A', '444444444', 'Garcia', null, null, null, null, null, null, null);
INSERT INTO usuarios VALUES ('55555555A', '555555555', 'Martinez', '2021-01-12 11:00:00', 'Wizink Center', 'Moderna', 'izquierdo', 'No', 'No', '2021-01-19 18:00:00');
INSERT INTO usuarios VALUES ('66666666A', '666666666', 'Rodriguez', '2021-04-01 14:00:00', 'Isabel Zendal', 'Pfizer', 'izquierdo', 'Si', 'No', '2021-05-01 12:00:00');
