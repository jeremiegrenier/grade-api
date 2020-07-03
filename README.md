# Grade API
> Api to manage grades for student.

This API manage grades for student.

## Installation

You need to [install docker](https://docs.docker.com/engine/install/) to use this project

```bash
git clone https://github.com/jeremiegrenier/grade-api.git

make install
```

## Usage example

You can find how to use this API with [postman collection](./postman/gradeAPI.postman_collection.json)

A [live example](http://gradeapi.jegr.ovh/api/ping) is available.

A [swagger](http://gradeapi.jegr.ovh/api/doc) for live example is also available.

_For more examples and usage, please refer to the [Wiki][wiki]._

## Development setup

```sh
make install
make docker-sh
```

## Run tests

```sh
make docker-test
```

## Meta

Grenier Jérémie – jeremiegrenier3@gmail.com

[wiki]: https://github.com/jeremiegrenier/grade-api/wiki
