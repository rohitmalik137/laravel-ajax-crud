<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
            integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
            crossorigin="anonymous"
        />
        <title>Todos</title>
    </head>
    <body>
        <div class="container">
            <h2>Add Todo</h2>
            <form name="todoForm" class="justify-content-center">
                <input type="hidden" name="todo_id" id="todo_id" />
                <div class="form-group">
                    <input
                        type="text"
                        id="name"
                        class="form-control col-sm-10"
                        placeholder="name"
                        name="name"
                    />
                </div>
                <div class="form-group">
                    <textarea
                        type="text"
                        id="description"
                        class="form-control col-sm-10"
                        placeholder="description"
                        rows="5"
                        cols="5"
                        name="description"
                    ></textarea>
                </div>
            </form>
            <button
                type="button"
                class="btn btn-primary"
                onclick="createTodo()"
            >
                Save
            </button>
            <hr />
            <div class="row justify-content-center my-5">
                <div class="col-md-8">
                    <div class="card card-default">
                        <div class="card-header">Todos</div>
                        <div class="card-body">
                            <ul class="list-group" id="showData">
                                @foreach($todos as $todo)
                                <li
                                    class="list-group-item"
                                    id="list_{{ $todo->id }}"
                                >
                                    <span id="name_{{ $todo->id }}">
                                        {{$todo->name}}</span
                                    >
                                    <span class="float-right">
                                        <button
                                            class="btn btn-primary btn-sm"
                                            data-id="{{ $todo->id }}"
                                            onclick="editTodo(event.target)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            data-id="{{ $todo->id }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="deleteTodo(event.target)"
                                        >
                                            Delete
                                        </button>
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- JS, Popper.js, and jQuery -->
    <script
        src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"
    ></script>
    <script
        src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"
    ></script>

    <script>
        function createTodo() {
            var name = $("#name").val();
            var description = $("#description").val();
            var todo_id = $("#todo_id").val();

            let _url = "/todos";
            let _token = $('meta[name="csrf-token"]').attr("content");

            $.ajax({
                url: _url,
                type: "POST",
                data: {
                    id: todo_id,
                    name: name,
                    description: description,
                    _token: _token,
                },
                success: function (response) {
                    // if (todo_id != "") {
                    $("#name_" + todo_id).innerHTML = response.name;
                    // } else {
                    var list = document.createElement("li");
                    list.className = "list-group-item";
                    list.setAttribute("id", "list_" + response.id);
                    list.innerHTML =
                        "<span id='name_'" +
                        response.id +
                        " >" +
                        response.name +
                        "</span>";

                    var spanEl = document.createElement("span");
                    spanEl.className = "float-right";

                    var editButtonEl = document.createElement("button");
                    editButtonEl.className = "btn btn-primary btn-sm";
                    editButtonEl.innerText = "Edit";
                    editButtonEl.setAttribute("data-id", response.id);
                    editButtonEl.setAttribute(
                        "onclick",
                        "editTodo(event.target)"
                    );
                    editButtonEl.type = "button";

                    var delButtonEl = document.createElement("button");
                    delButtonEl.className = "btn btn-danger btn-sm";
                    delButtonEl.innerText = "Delete";
                    delButtonEl.type = "button";
                    delButtonEl.setAttribute("data-id", response.id);
                    delButtonEl.setAttribute(
                        "onclick",
                        "deleteTodo(event.target)"
                    );

                    spanEl.append(editButtonEl);
                    spanEl.append(delButtonEl);
                    list.append(spanEl);

                    if (todo_id != "") {
                        $("#list_" + todo_id).replaceWith(list);
                    } else {
                        $("#showData").prepend(list);
                    }
                    // }
                    $("#name").val("");
                    $("#description").val("");
                },
                error: function () {
                    alert("An error was encountered.");
                },
            });
        }

        function deleteTodo(event) {
            var id = $(event).data("id");
            let _url = `/todos/${id}`;
            let _token = $('meta[name="csrf-token"]').attr("content");

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                type: "DELETE",
                url: _url,
                success: function (response) {
                    $("#list_" + id).remove();
                },
            });
        }

        function editTodo(event) {
            var id = $(event).data("id");
            let _url = `/todos/${id}/edit`;

            $.ajax({
                url: _url,
                type: "GET",
                success: function (response) {
                    if (response) {
                        $("#todo_id").val(response.id);
                        $("#name").val(response.name);
                        $("#description").val(response.description);
                    }
                },
            });
        }
    </script>
</html>
