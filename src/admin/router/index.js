import Vue from "vue";
import Router from "vue-router";
import FormBuilder from "../pages/FormBuilder.vue";
import ReportBuilder from "../pages/ReportBuilder.vue";
import Settings from "../pages/Settings.vue";
import Reports from "../pages/Reports.vue";

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: "/forms/create",
      name: "Create form",
      component: FormBuilder
    },
    {
      path: "/forms/:id",
      name: "Edit form",
      component: FormBuilder
    },
    {
      path: "/reports/:formId/create",
      name: "Edit report",
      component: ReportBuilder
    },
    {
      path: "/reports/:formId/:reportId",
      name: "Edit report",
      component: ReportBuilder
    },
    {
      path: "/reports",
      name: "Reports",
      component: Reports
    },
    {
      path: "/settings",
      name: "Settings",
      component: Settings
    }
  ]
});
