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
        <div class="row">
            <div class="col-lg-4" v-for="desk_list in desk_lists">
                <div class="card mt-3 p-3">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-pen text-success" style="font-size: 15px; cursor: pointer; margin-left: auto;" @click="desk_list_input_id = desk_list.id"></i>
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 15px; cursor: pointer; margin-left: 10px;" @click="deleteDeskList(desk_list.id)"></i>
                        </div>
                        <small>{{ new Date(desk_list.created_at).toLocaleString()}}</small>

                    <form @submit.prevent="updateDescList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="mt-3 d-flex align-items-center">
                        <input type="text" class="form-control" v-model="desk_list.name" placeholder="Введите название списка" >
                        <i class="fa-solid fa-xmark-large text-danger" style="font-size: 17px; cursor: pointer; margin-left: 10px;" @click="desk_list_input_id = null">&times;</i>
                    </form>

                    <h2 v-else class="card-title d-flex justify-content-between">{{ desk_list.name }} </h2>
                    <!--<form @submit.prevent="addNewCard(desk_list.id)" >
                        <div class="mt-3">
                            <input type="text" class="form-control" v-model="card_names[desk_list.id]" :class="{ 'is-invalid': $v.card_names.$each[desk_list.id].$error }"placeholder="" >
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].required">
                                Обязательное поле
                            </div>
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].maxLength">
                                Максимальное кол-во символов: {{$v.card_names.$each[desk_list.id].$params.maxLength.max}}
                            </div>
                        </div>
                    </form>-->
                    <ul class="list-group list-group-flush my-3">
                        <li class="list-group-item" v-for="card in desk_list.cards" :key="card.id">
                            {{ card.name }}
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных
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
            card_names: []
        }
    },
    methods: {
       /* addNewCard(desk_list_id) {
            this.$v.card_names.$each[desk_list_id].$touch()
            if(this.$v.card_names.$each[desk_list_id].$anyError) {
                return;
            }
        },*/
        updateDescList(id, name) {
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
                        this.card_names[el.id] = ''
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
                    //this.desk_list_name = '';
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
        }
    }
}
</script>

