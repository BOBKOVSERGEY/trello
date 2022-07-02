<template>
    <div class="container">
        <h1>Доски</h1>
        <form @submit.prevent="addNewDesk">
            <div class="mb-3">
                <input type="text" class="form-control" v-model="name" :class="{ 'is-invalid': $v.name.$error }" placeholder="Введите название достки" >
                <div class="invalid-feedback" v-if="!$v.name.required">
                    Обязательное поле
                </div>
                <div class="invalid-feedback" v-if="!$v.name.maxLength">
                    Максимальное кол-во символов: {{$v.name.$params.maxLength.max}}
                </div>
            </div>

            <button type="submit" v-model="name" class="btn btn-success">Добавить доску</button>
        </form>
        <div class="alert alert-danger mt-3" role="alert" v-if="errored">
            Ошибка загрузки данных<br>
            {{ errors[0] }}
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <router-link class="card-body nav-link" :to="{name: 'showDesk', params: {deskId: desk.id}}">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 15px; cursor: pointer; margin-left: auto;" @click="deleteDesk(desk.id)"></i>
                        </div>
                        <small>{{ new Date(desk.created_at).toLocaleString()}}</small>
                        <h2 class="card-title">{{ desk.name }}</h2>
                    </router-link>
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
    data() {
        return {
            desks: [],
            errored: false,
            errors: [],
            loading: true,
            name: null
        }
    },
    methods: {
        getAllDesks() {
            axios.get('/api/V1/desks')
                .then(response => {
                    this.desks = response.data.data
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
        deleteDesk(id) {
            if(confirm('Вы действительно хотите удалить доску?')) {
                axios.post('/api/V1/desks/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desks = [];
                        this.getAllDesks();
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
        },
        addNewDesk() {
            this.$v.$touch()
            if(this.$v.$anyError) {
                return;
            }
            axios.post('/api/V1/desks/', {
                name: this.name
            })
                .then(response => {
                    this.desks = [];
                    this.getAllDesks();
                    this.errored = false
                    //this.name = '';
                })
                .catch(error => {
                    console.log(error.response);
                    if(error.response.data.errors.name) {
                        this.errors = [];
                        this.errors.push(error.response.data.errors.name[0])
                    }
                    this.errored = true
                })
                .finally(() => {
                    // когда все загрузилось
                    this.loading = false
                })
        }
    },
    mounted() {
        this.getAllDesks();
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(255)
        }
    }
}
</script>

