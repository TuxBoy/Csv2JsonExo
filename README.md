# Csv2Json commandes

## La commande csv2Json

- Génération du json de base.

```bash
$ ./console csv2json --file=demo.csv
```

### Options disponibles

| Options        | Paramètres                   | Description                               |
| -------------- | ---------------------------  |:-----------------------------------------:|
| `--pretty`     |      _                       | Permet de mieux formater le json          |
| `--aggregate`  |  field                       | Aggrège les données sur un champ          |
| `--fields`     | "field1,field2,field3,..."   | Liste des champs à sortir dans le json    |
| `--desc`       | "file"                     | Fichier de description des types de champ |

### Exemple

```bash
$ ./console csv2json --file=demo.csv --aggregate=name --pretty
```

**Output:**

```json
{
  "foo": [
    {"id": 5, "date": "2020-05-03"},
    {"id": 9, "date": "2020-05-03"},
    {"id": 12, "date": "2020-05-07"}
  ],
  "bar": [
    {"id": 1, "date": "2020-03-21"}
  ],
  "boo": [
    {"id": 4, "date": "2020-03-14"},
    {"id": 5, "date": "2020-02-19"}
  ],
  "far": [
    {"id": 10, "date": "2020-04-30"}
  ]
}

```

## Lancer les tests

```bash
$ ./console unit-test
```

ou avec docker 

```bash
$ USER_ID=$(id -u) GROUP_ID=$(id -g) docker-compose run php bash -c "php ./console unit-test" 
```
