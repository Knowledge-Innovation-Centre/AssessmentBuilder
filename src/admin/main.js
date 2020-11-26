import "../../assets/scss/admin.scss";

import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import menuFix from "./utils/admin-menu-fix";
import Generic from "./components/Generic.vue";
import VueTippy, { TippyComponent } from "vue-tippy";
import Vuex from "vuex";
import store from "./store";
import Notifications from "vue-notification";
import commonMixin from "./mixins/commonMixin";

Vue.config.productionTip = false;

Vue.use(Notifications);
Vue.use(Vuex);
Vue.use(VueTippy);
Vue.component("tippy", TippyComponent);
Vue.component("generic", Generic);
Vue.mixin(commonMixin);

if (document.getElementById("vue-admin-app")) {
  new Vue({
    store,
    el: "#vue-admin-app",
    router,
    render: h => h(App)
  });
}

// fix the admin menu for the slug "vue-app"
menuFix("vue-app");
