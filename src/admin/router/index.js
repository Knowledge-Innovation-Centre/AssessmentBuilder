import Vue from 'vue'
import Router from 'vue-router'
import FormBuilder from 'admin/pages/FormBuilder.vue'
import Settings from 'admin/pages/Settings.vue'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/forms/create',
      name: 'Home',
      component: FormBuilder
    },
    {
      path: '/forms/:id',
      name: 'Home',
      component: FormBuilder
    },
    {
      path: '/settings',
      name: 'Settings',
      component: Settings
    },
  ]
})
