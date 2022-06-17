<template>
    <div class="container">
        <h1>Достки</h1>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <a href="#" class="card-body">
                        <h5 class="card-title">{{ desk.name }}</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </a>
                </div>
            </div>

        </div>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            desks: [],
            errored: false
        }
    },
    mounted() {
        axios.get('/api/V1/desks')
        .then(response => {
            this.desks = response.data.data
        })
        .catch(error => {
            console.log(error);
            this.errored = true
        })
    }
}
</script>

