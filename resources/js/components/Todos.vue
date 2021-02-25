<template>
    <div class="container">
        <div class="all">
            <h1>All tasks</h1>

            <div v-if="errored" class="alert alert-danger" role="alert">
                Tasks didn't load. Try later :(
            </div>

            <table v-else class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Task</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="todo in todos" v-if="todo.is_complete == 0" :key="todo.id">
                    <th scope="row">{{ todo.id }}</th>
                    <td>{{ todo.task }}</td>
                    <td>
                        <button class="btn btn-success" @click="addTodo = false; addOrEditTodo(todo.id, true)">
                            <i class="fa fa-check"></i>
                        </button>
                        <button class="btn btn-primary" @click="addTodo = false; update(todo)" onclick="$('#add-edit-modal').modal('show')">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger"
                                @click="deleteTodo(todo.id)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <nav class="spcbtw" aria-label="page navigation">
                <div>
                    <ul class="pagination">
                        <li class="page-item"
                            :class="{ disabled: !pagination.prev_page_url }"
                            @click.prevent="pagination.prev_page_url ? getTodos(pagination.prev_page_url) : null">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li v-for="page in pagination.last_page" :key="page"
                            class="page-item"
                            :class="{ active: pagination.current_page == page}"
                            @click.prevent="getTodos(pageURL + page)">
                            <a href="#" class="page-link">{{ page }}</a>
                        </li>
                        <li class="page-item"
                            :class="{ disabled: !pagination.next_page_url }"
                            @click.prevent="pagination.next_page_url ? getTodos(pagination.next_page_url) : null">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <button type="button" class="btn btn-success" @click="addTodo = true; todo.task = ''" onclick="$('#add-edit-modal').modal('show')">
                        Add a task
                    </button>
                </div>
            </nav>

            <div id="add-edit-modal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ addTodo ? 'Add task' : 'Edit task' }}</h5>
                        </div>
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="inputGroup-sizing-default">Task</span>
                                <input v-model="todo.task" id="task-value" type="text" class="form-control" aria-label="Sizing example input"
                                       aria-describedby="inputGroup-sizing-default">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="$('#add-edit-modal').modal('hide')">Close</button>
                            <button type="button" class="btn btn-primary" @click="addOrEditTodo(todo.id)">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="completed">
            <h1>Completed tasks</h1>

            <div v-if="errored" class="alert alert-danger" role="alert">
                Tasks didn't load. Try later :(
            </div>

            <div v-for="todo in completed" class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">{{ todo.id }}</span>
                </div>
                <div class="form-control" aria-label="Username" aria-describedby="basic-addon1">{{ todo.task }}</div>
                <button class="btn btn-danger"
                        @click="deleteTodo(todo.id)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            todo: {
                id: '',
                task: '',
            },
            todos: [],
            completed: [],
            pageURL: 'api/todos?page=',
            pagination: {},
            errored: false,
            addTodo: false,
        }
    },
    mounted() {
        this.getTodos();
        this.getCompleted();
    },
    methods: {
        getTodos(page_url = 'api/todos') {
            axios
                .get(page_url)
                .then(response => {
                    this.todos = response.data.data;
                    let Pagination = this.pagination;
                    let Data = response.data;
                    Pagination.current_page = Data.current_page;
                    Pagination.last_page = Data.last_page;
                    Pagination.next_page_url = Data.next_page_url;
                    Pagination.prev_page_url = Data.prev_page_url;
                })
                .catch(error => this.errored = true);
        },
        getCompleted() {
            axios
                .get('api/completed')
                .then(response => {
                    this.completed = response.data;
                })
                .catch(error => this.errored = true);
        },
        addOrEditTodo(id, is_check = false) {
            if (this.addTodo) {
                axios
                    .post('api/todos', {
                        todo: this.todo.task,
                    })
                    .then(response => {
                        this.getTodos(this.pageURL + this.pagination.current_page);
                        this.todo.task = '';
                        $('#add-edit-modal').modal('hide');
                    })
                    .catch(error => console.log(error));
            } else {
                axios
                    .put(`api/todos/${id}`, {
                        todo: this.todo.task,
                        check: is_check,
                    })
                    .then(response => {
                        this.getTodos(this.pageURL + this.pagination.current_page);
                        this.getCompleted();
                        this.todo.task = '';
                        $('#add-edit-modal').modal('hide');
                    })
                    .catch(error => console.log(error));
            }
        },
        deleteTodo(id) {
            axios
                .delete(`api/todos/${id}`)
                .then(response => {
                    this.getTodos(this.pageURL + this.pagination.current_page);
                    this.getCompleted();
                })
                .catch(error => console.log(error));
        },
        update(todo) {
            this.todo.id = todo.id;
            this.todo.task = todo.task;
            $('#task-value').val(todo.task);
        },
    }
}
</script>

<style scoped>
.spcbtw {
    display: flex;
    justify-content: space-between;
}

.completed {
    margin-top: 30px;
}
</style>
