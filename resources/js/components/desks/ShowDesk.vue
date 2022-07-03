<template>
    <div class="container">
        <h1>{{name}}</h1>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Введите название доски</label>
            <input type="text" @blur="saveName" v-model="name"  class="form-control " :class="{ 'is-invalid': $v.name.$error }" id="exampleFormControlInput1">
            <div class="invalid-feedback" v-if="!$v.name.required">
                Обязательное поле
            </div>
            <div class="invalid-feedback" v-if="!$v.name.maxLength">
                Максимальное кол-во символов: {{$v.name.$params.maxLength.max}}
            </div>
        </div>
        <form @submit.prevent="addNewDeskList">
            <div class="mb-3">
                <label for="exampleFormControlInput2" class="form-label">Введите название списка</label>
                <input type="text" class="form-control" v-model="desk_list_name" :class="{ 'is-invalid': $v.desk_list_name.$error }" placeholder="Введите название списка" id="exampleFormControlInput2">
                <div class="invalid-feedback" v-if="!$v.desk_list_name.required">
                    Обязательное поле
                </div>
                <div class="invalid-feedback" v-if="!$v.desk_list_name.maxLength">
                    Максимальное кол-во символов: {{$v.desk_list_name.$params.maxLength.max}}
                </div>
            </div>

            <button type="submit" v-model="desk_list_name" class="btn btn-success">Добавить список</button>
        </form>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="(desk_list, index) in desk_lists">
                <div class="card mt-3 p-3">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-pen text-success" style="font-size: 15px; cursor: pointer; margin-left: auto;" @click="desk_list_input_id = desk_list.id"></i>
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 15px; cursor: pointer; margin-left: 10px;" @click="deleteDeskList(desk_list.id)"></i>
                        </div>
                        <small>{{ new Date(desk_list.created_at).toLocaleString()}}</small>

                    <form @submit.prevent="updateDescList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="mt-3 d-flex align-items-center">
                        <input type="text" class="form-control" v-model="desk_list.name" placeholder="Введите название списка" :class="{ 'is-invalid': $v.desk_lists.$each[index].name.$error }">
                        <i class="fa-solid fa-xmark-large text-danger" style="font-size: 17px; cursor: pointer; margin-left: 10px;" @click="updateDescList(desk_list.id, desk_list.name)">&times;</i>
                    </form>

                    <h2 v-else class="card-title d-flex justify-content-between">{{ desk_list.name }} </h2>
                    <form @submit.prevent="addNewCard(desk_list.id)" >
                        <div class="mt-3">
                            <input type="text" class="form-control" v-model="card_names[desk_list.id]" :class="{ 'is-invalid': $v.card_names.$each[desk_list.id].$error }" placeholder="Введите название новой карточки" >
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].required">
                                Обязательное поле
                            </div>
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].maxLength">
                                Максимальное кол-во символов: {{$v.card_names.$each[desk_list.id].$params.maxLength.max}}
                            </div>
                        </div>
                    </form>
                    <!-- Button trigger modal -->


                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <form @submit.prevent="updateCardName" v-if="show_card_name_input" class="mt-3 d-flex align-items-center">
                                        <input type="text" class="form-control" v-model="current_card.name" :class="{ 'is-invalid': $v.current_card.name.$error }" placeholder="Введите название карточки" >
                                        <i class="fa-solid fa-xmark-large text-danger" @click="updateCardName" style="font-size: 17px; cursor: pointer; margin-left: 10px;">&times;</i>
                                    </form>

                                    <h5 class="modal-title d-flex align-items-center" id="staticBackdropLabel" v-if="!show_card_name_input">
                                        <i class="fa-solid fa-pen text-success" style="font-size: 15px; cursor: pointer; margin-right: 10px;" @click="show_card_name_input = true"></i>
                                        <span>{{ current_card.name }}</span>
                                    </h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form @submit.prevent="addNewTask">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput4" class="form-label">Введите название списка</label>
                                            <div class="d-flex align-items-center">
                                                <input type="text" class="form-control" v-model="new_task_name" :class="{ 'is-invalid': $v.new_task_name.$error }"   placeholder="Введите название задачи" id="exampleFormControlInput4">
                                                <i class="fa-solid fa-floppy-disk text-success" @click="addNewTask" style="font-size: 17px; cursor: pointer; margin-left: 10px;"></i>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="mb-3 form-check" v-for="(task, index) in current_card.tasks">
                                        <div class="d-flex align-items-center">

                                            <form @submit.prevent="updateTask(current_card.tasks[index])" v-if="task_input_name_id === task.id" style="width: 100%; margin-right: 10px; margin-left: -24px">
                                                <input type="text" class="form-control" v-model="current_card.tasks[index].name"  placeholder="Введите название карточки" >
                                            </form>

                                            <div v-else>
                                                <input type="checkbox" class="form-check-input" @change="updateTask(current_card.tasks[index])" :id="'exampleInputPassword'+task.id" v-model="current_card.tasks[index].is_done">
                                                <label class="form-check-label" :for="'exampleInputPassword' +task.id">{{ task.name }}</label>
                                            </div>
                                            <div class="d-flex flex-column align-items-center" style="margin-left: auto">
                                                <i class="fa-solid fa-pen text-success" v-if="task_input_name_id !== task.id" @click="task_input_name_id = task.id" style="font-size: 9px; cursor: pointer; margin-bottom: 5px;" ></i>
                                                <i class="fa-solid fa-floppy-disk text-info" @click="updateTask(current_card.tasks[index])" style="font-size: 9px; cursor: pointer; margin-bottom: 5px;"></i>
                                                <i class="fa-solid fa-xmark-large text-danger" @click="deleteTask(task.id)" style="font-size: 12px; cursor: pointer;">&times;</i>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush my-3">
                        <li class="list-group-item d-flex align-items-center" v-for="card in desk_list.cards" :key="card.id">
                            <div style="margin-right: 5px; cursor: pointer; width: 100%;" @click="getCard(card.id)" data-bs-toggle="modal" data-bs-target="#staticBackdrop">{{ card.name }}</div>
                            <i class="fa-solid fa-trash-can text-secondary" style="font-size: 12px; cursor: pointer; margin-left: auto;" @click="deleteCard(card.id)"></i>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <div style="text-align: center;" v-if="loading">
            <div class="spinner-border" style="width: 4rem; margin: 0 auto; height: 4rem;" role="status" >
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
import { required, maxLength } from 'vuelidate/lib/validators';
export default {
    props: [
        'deskId'
    ],
    data() {
        return {
            name: null,
            desk_list_name: null,
            errored: false,
            loading: true,
            desk_lists: [],
            desk_list_input_id: null,
            card_names: [],
            current_card: [],
            show_card_name_input: false,
            new_task_name: '',
            task_input_name_id: null
        }
    },
    methods: {
        updateTask(task) {
            axios.post('/api/V1/tasks/' + task.id, {
                _method: 'PATCH',
                name: task.name,
                is_done: task.is_done,
                card_id: task.card_id,
            })
                .then(response => {
                    this.task_input_name_id = null
                    //this.$v.$reset();
                    //this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
        },
        deleteTask(id) {
            axios.post('/api/V1/tasks/' + id, {
                _method: 'DELETE'
            })
                .then(response => {
                    this.getCard(this.current_card.id);
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
        addNewTask() {
            this.$v.new_task_name.$touch()
            if(this.$v.new_task_name.$anyError) {
                return;
            }
            axios.post('/api/V1/tasks/', {
                name: this.new_task_name,
                card_id: this.current_card.id
            })
                .then(response => {
                    this.new_task_name =  '';
                    this.$v.$reset();
                    this.getCard(this.current_card.id);
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
        updateCardName() {
            this.$v.current_card.name.$touch()
            if(this.$v.current_card.name.$anyError) {
                return;
            }
            this.show_card_name_input = false
            axios.post('/api/V1/cards/' + this.current_card.id, {
                _method: 'PATCH',
                name: this.current_card.name,
                desk_list_id: this.current_card.desk_list_id,
            })
                .then(response => {
                    this.show_card_name_input = false;
                    this.$v.$reset();
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
        },
        getCard(id) {
            axios.get('/api/V1/cards/' + id)
                .then(response => {
                    this.current_card = response.data.data
                    //console.log(this.current_card)
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
        deleteCard(id) {
            axios.post('/api/V1/cards/' + id, {
                _method: 'DELETE'
            })
                .then(response => {
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
       addNewCard(desk_list_id) {

            this.$v.card_names.$each[desk_list_id].$touch()
            if(this.$v.card_names.$each[desk_list_id].$anyError) {
                return;
            }
           axios.post('/api/V1/cards/', {
               name: this.card_names[desk_list_id],
               desk_list_id
           })
               .then(response => {
                   this.$v.$reset();
                   this.getDeskLists()
               })
               .catch(error => {
                   console.log(error);
                   this.errored = true
               })
               .finally(() => {
                   // когда все загрузилось
                   this.loading = false
               })
        },
        updateDescList(id, name) {
            this.$v.desk_lists.$touch()
            if(this.$v.desk_lists.$anyError) {
                return;
            }

            //$v.desk_lists.$each[index].name
            this.desk_list_input_id = null;
            axios.post('/api/V1/desk-lists/'+id, {
                _method: 'PUT',
                name: name
            })
                .then(response => {
                    this.desk_list_input_id = null
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
        getDeskLists() {
            axios.get('/api/V1/desk-lists/', {
                params: {
                    desk_id: this.deskId
                }
            })
                .then(response => {
                    this.desk_lists = response.data.data
                    this.desk_lists.forEach(el=> {
                        //this.card_names[el.id] = ''
                        this.$set(this.card_names, el.id, '')
                    })
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
          saveName() {
              this.$v.name.$touch()
              if(this.$v.name.$anyError) {
                  return;
              }
              axios.post('/api/V1/desks/'+this.deskId, {
                  _method: 'PUT',
                  name: this.name
              })
                  .then(response => {
                      this.$v.$reset();
                  })
                  .catch(error => {
                      console.log(error);
                      this.errored = true
                  })
                  .finally(() => {
                      // когда все загрузилось
                      this.loading = false
                  })
          },
        addNewDeskList() {
            this.$v.desk_list_name.$touch()
            if(this.$v.desk_list_name.$anyError) {
                return;
            }
            axios.post('/api/V1/desk-lists/', {
                name: this.desk_list_name,
                desk_id: this.deskId
            })
                .then(response => {
                    this.$v.$reset();
                    this.desk_list_name = '';
                    this.desk_lists = [];
                    this.getDeskLists();
                    this.errored = false
                })
                .catch(error => {
                    console.log(error.response);

                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        },
        deleteDeskList(id) {
            if(confirm('Вы действительно хотите удалить список?')) {
                axios.post('/api/V1/desk-lists/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desk_lists = [];
                        this.getDeskLists();
                    })
                    .catch(error => {
                        console.log(error);
                        this.errored = true
                    })
                    .finally(() => {
                        // когда все загрузилось
                        this.loading = false
                    })
            }
        }
    },
    mounted() {
        axios.get('/api/V1/desks/'+this.deskId)
            .then(response => {
                this.name = response.data.data.name
            })
            .catch(error => {
                console.log(error);
                this.errored = true
            })
            .finally(() => {
                // когда все загрузилось
                this.loading = false
            })

        this.getDeskLists();
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(255)
        },
        desk_list_name: {
            required,
            maxLength: maxLength(255)
        },
        card_names: {
            $each: {
                required,
                maxLength: maxLength(255)
            }
        },
        current_card: {
            name: {
                required,
                maxLength: maxLength(255)
            }
        },
        current_task_name: {
            name: {
                required,
                maxLength: maxLength(255)
            }
        },
        new_task_name: {
            required,
            maxLength: maxLength(255)
        },
        desk_lists: {
            $each: {
                name: {
                    required,
                    maxLength: maxLength(255)
                }
            }
        }
    }
}
</script>

