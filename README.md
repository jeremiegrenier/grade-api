# Grade API
> Api to manage grades for student.

This API manage grades for student.

## Installation

You need to [install docker](https://docs.docker.com/engine/install/) to use this project.

Docker images could be found on [docker hub](https://hub.docker.com/r/jeremiegrenier/grade-api)

```bash
git clone https://github.com/jeremiegrenier/grade-api.git

make install
```

## Usage example

You can find how to use this API with [postman collection](./postman/gradeAPI.postman_collection.json)

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
