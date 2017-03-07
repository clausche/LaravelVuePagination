<template>
    <div class="dv">

        <div class="dv-header row">
            <div class="col-md-2">Customer Data</div>
            <div class="col-md-1">
                Search : 
            </div>
            <div class="col-md-2">
                <select class="dv-header-select form-control" v-model="query.search_column">
                    <option v-for="column in columns" :value="column">{{ column }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="dv-header-select form-control" v-model="query.search_operator">
                    <option v-for="(value,key) in operators" :value="key">{{ value }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="dv-header-input form-control" name="" placeholder="Search ..." v-model="query.search_term"/>
            </div>
            <div class="col-md-1">
                <button class="dv-header-btn btn btn-primary" name="" @click="filterData()"> <i class="glyphicon glyphicon-filter"></i> Filter</button>
            </div>
        </div>

        <!-- <div class="dv-header">

            <div class="dv-header-title">
                {{title}}
            </div>

            <div class="dv-header-columns">
                <span>Search : </span>
                <select class="dv-header-select form-control">
                    <option v-for="column in columns" :value="column">{{ column }}</option>
                </select>
            </div>

            <div class="dv-header-operators">
                <select class="dv-header-select form-control">
                    <option>=</option>
                </select>
            </div>

            <div class="dv-header-search">
                <input type="text" class="dv-header-input form-control" name="" placeholder="Search ...">
            </div>

            <div class="dv-header-submit">
                <button class="dv-header-btn btn btn-primary" name="">Filter</button>
            </div>
        </div> -->

        <div class="dv-body">
            <table class="dv-table table table-hover">
                <thead>
                    <tr>
                        <th v-for="column in columns" @click="toggleOrder(column)" style="cursor: pointer">
                            <span>{{ column }}</span> 
                            <span v-if="column == query.column">
                                <span v-if="query.direction == 'desc'">&darr;</span>
                                <span v-else="query.direction == 'asc'">&uarr;</span>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in model.data"> 
                        <td v-for="(value,key) in row">{{ value }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6">
                Showing {{ model.from }} - {{ model.to }} of {{ model.total }} rows
            </div>
            <div class="col-md-3">
                Rows per page <input type="text" v-model="query.per_page" @keyup="fetchIndexData()" style="width:40px">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary" @click="prev()">&laquo; Prev</button>
                <input type="text" v-model="query.page" style="width:40px" @keyup="fetchIndexData()"/>
                <button class="btn btn-primary" @click="next()">&raquo; Next</button>
            </div>
        </div>
        
    </div>
</template>

<script>
    export default {

        props: ['source','title'],

        data() {
            return {
                model: {},
                columns: {},
                query: {
                    page: 1,
                    column: 'id',
                    direction: 'desc',
                    per_page: 10,
                    search_column: 'name',
                    search_operator: 'like',
                    search_term: '',
                },
                operators: {
                    'equal': '=',
                    'not_equal': '<>',
                    'less_than': '<',
                    'greater_than': '>',
                    'less_than_or_equal_to': '<=',
                    'greater_than_or_equal_to': '>=',
                    'in': 'IN',
                    'like': 'LIKE',
                }
            }
        },

        created() {
            this.fetchIndexData()
        },

        methods: {

            filterData() {
                this.query.page = 1;
                this.fetchIndexData()
            },
            prev() {
                this.query.page--;
                this.fetchIndexData()
            },
            next() {
                this.query.page++;
                this.fetchIndexData()
            },
            toggleOrder(column) {

                if(column == this.query.column){

                    if(this.query.direction == 'desc'){
                        this.query.direction = 'asc'
                    }else{
                        this.query.direction = 'desc'
                    }

                }else{
                    this.query.column = column
                    this.query.direction = 'asc'
                }

                this.fetchIndexData();
                
            },
            fetchIndexData() {

                var vm = this

                axios.get(`${this.source}?column=${this.query.column}&direction=${this.query.direction}&page=${this.query.page}&per_page=${this.query.per_page}&search_column=${this.query.search_column}&search_operator=${this.query.search_operator}&search_term=${this.query.search_term}`)

                .then(function(response) {
                    Vue.set(vm.$data,'model', response.data.model)
                    Vue.set(vm.$data,'columns', response.data.columns)
                })

                .catch(function(response) {
                    console.log(response);
                })
            }
        }

    }
</script>
