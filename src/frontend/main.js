import '../../assets/scss/frontend.scss'
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from "./store";
import Vuex from 'vuex'


Vue.config.productionTip = false

Vue.use(Vuex)

if(document.getElementById("vue-frontend-app")) {
  new Vue({
    store,
    el: '#vue-frontend-app',
    router,
    render: h => h(App)
  })
}
