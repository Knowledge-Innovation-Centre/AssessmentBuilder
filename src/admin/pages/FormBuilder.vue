<template>
  <div class="aoat-bg-white aoat-rounded aoat-p-6 aoat-container">
    <div class="aoat-flex">
      <div class="aoat-pr-6 aoat-flex-1">
        <div class="aoat-bg-white aoat-grid aoat-grid-cols-2 aoat-gap-10 aoat-mb-5">
          <div>
            <h2 class="aoat-mt-0 aoat-text-gray-700" v-if="id">Editing form: {{title}}</h2>
            <h2 class="aoat-mt-0 aoat-text-gray-700" v-else>Creating new form</h2>
            <label class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mb-2">
              Title
            </label>
            <input v-model="title"
                   type="text"
                   class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white">
            <label class="aoat-mb-5  aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold">
                <input v-model="formSettings.showPageNumbers" type="checkbox">
                  Show page numbers
                </label>
          </div>
          <div>
            <template v-if="id">
              <h2 class="aoat-mt-0 aoat-text-gray-700 aoat-flex aoat-flex-row aoat-justify-between">
                Reports
                <router-link  :to="'/reports/' + id + '/create'"
                              class="aoat-no-underline aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
                +</router-link>
              </h2>
              <div :key="report.ID" class="aoat-flex aoat-flex-row aoat-justify-between" v-for="report in reports">
                <router-link :to="'/reports/' + id + '/' + report.ID"
                             class="aoat-text-gray-800 aoat-font-semibold">

                  {{ report.post_title }}
                </router-link>
                <div>
                <div>
                  <button @click="activateReport(report.ID)"
                          :class="report.post_status === 'publish' ? 'aoat-bg-green-200' : 'aoat-bg-white'"
                          class="aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
                    <span class="dashicons dashicons-saved"></span>
                  </button>
                  <button @click="duplicateReport(report.ID)"
                          class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
                    <span class="dashicons dashicons-admin-page"></span>
                  </button>
                  <button @click="removeReport(report.ID)"
                          class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
                    <span class="dashicons dashicons-trash"></span>
                  </button>
                </div>
                </div>
              </div>

            </template>
          </div>
        </div>
        <div class="aoat-text-center aoat-my-3">
          <code>[aoat-form id="{{ form.ID }}"]</code>
          <code>[aoat-assessment-list id="{{ form.ID }}"]</code>
        </div>
        <generic :depth="0" :form="formData" class="root"></generic>
        <div class="aoat-text-center aoat-mt-5">
          <button @click="save()"
                  class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-py-2 aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
            Save
          </button>

          <button @click="downloadJson()"
                  class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-py-2 aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
            Export
          </button>
          <tippy arrow
                 ref="import_json"
                 :interactive="true"
                 theme="light"
                 :max-width="800"
                 placement="left"
                 class="aoat-inline-block"
                 trigger="click">
            <template v-slot:trigger>
              <button class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-py-2 aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
                Import...
              </button>
            </template>
            <textarea v-model="importJson"cols="30" rows="10"></textarea>
            <button class="aoat-mt-2" @click="importData()">Import</button>
          </tippy>
        </div>
      </div>
      <div class="aoat-h-full aoat-bg-gray-300 aoat-top-2 aoat-rounded aoat-sticky aoat-p-4 aoat-max-h-screen aoat-overflow-y-scroll">
        <div class="aoat-text-center aoat-mb-6">

          <button @click="save()"
                  class="aoat-bg-white aoat-py-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
            Save
          </button>
        </div>
        <h2 class="aoat-mt-0">Builder elements</h2>
        <div>
          <drag
              v-for="(element) in availableBuilderElements"
              :key="element.key"
              class="aoat-bg-white aoat-py-2 aoat-text-center aoat-mb-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
              :data="element"
              @cut="remove(element)">
            {{element.name}}
          </drag>
        </div>
        <h2 class="aoat-mt-6">Form elements</h2>
        <drag v-for="(element) in availableFormElements"
              :key="element.key"
              class="aoat-bg-white aoat-py-2 aoat-text-center aoat-mb-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
              :data="element"
              @cut="remove(element)">
          {{element.name}}
        </drag>
      </div>
    </div>

  </div>
</template>

<script>
  import { Drag } from "vue-easy-dnd";
  import { isEmpty } from "lodash";
  import Api from "../Api";
  import formElements from "../utils/form-elements"
  import randomValueHex from "../utils/helpers"
  import Generic from "../components/Generic.vue";

  let isDirty = false

  window.onload = function() {
    window.addEventListener("beforeunload", function (e) {
      if (!isDirty) {
        return undefined;
      }

      let confirmationMessage = 'It looks like you have been editing something. '
          + 'If you leave before saving, your changes will be lost.';

      (e || window.event).returnValue = confirmationMessage; //Gecko + IE
      return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
  };
export default {

  name: 'Home',
  components: {
    Drag,
    Generic
  },
  data () {
    return {
      addedElements: [],
      title: "",
      importJson: "",
      formData: {},
      form: {},
      reports: [],
      formSettings: {
        showPageNumbers: false
      },
    }
  },
  computed: {
    id() {
      return this.$route.params.id
    },
    availableFormElements() {
      let filteredFormElements = formElements.filter(element => !['part_score','total_score','total_score_graph'].includes(element.type))
      return filteredFormElements.filter(element => !['column','row','page','paragraph'].includes(element.type))
    },
    availableBuilderElements() {
      let filteredFormElements = formElements.filter(element => !['part_score','total_score','total_score_graph'].includes(element.type))
      return filteredFormElements.filter(element => ['column','row','page','paragraph'].includes(element.type))
    }
  },
  mounted() {
  },
  created() {
    this.loadForm()
  },
  watch: {
    formData: {
      deep: true,
      handler(newData, oldData) {
        this.$store.dispatch('updateForm', this.formData);

        this.$store.dispatch('updateReport', {});

        if (!isEmpty(oldData)) {
          isDirty = true
        }
      }
    },
    id() {
      this.loadForm()
    }
  },
  methods: {
    loadForm() {
      if (! this.id) {
        this.formData = {
          key: randomValueHex(10),
          component: "Form",
          name: "Form",
          type: "form",
          conditions: [],
          items: [
            // page
          ]
        }
        return;
      }
      Api.get(aoat_config.aoatGetFormUrl + this.id).then((result) => {
        this.form  = result.data;
        this.formData  = this.form.form_data[0];
        this.title = this.form.post_title;
        this.formSettings = this.form.form_settings[0];
        this.reports = this.form.reports
        this.$store.dispatch('updateSettings', this.form.settings)
      })
    },
    remove(n) {
      let index = this.addedElements.indexOf(n);
      this.addedElements.splice(index, 1);
    },
    removeReport(reportId) {
      Api.delete(aoat_config.aoatDeleteReportUrl + reportId).then((result) => {
        this.reports = this.reports.filter(report => report.ID !== reportId)

        this.$notify({
          title: 'Report deleted',
          type: 'success',
        })
      })
    },
    duplicateReport(reportId) {
      Api.post(aoat_config.aoatDuplicateReportUrl, {
        id: reportId
      }).then((result) => {
        this.reports.push(result.data)
        this.$notify({
          title: 'Report duplicated',
          type: 'success',
        })
      })
    },
    activateReport(reportId) {
      Api.post(aoat_config.aoatActivateReportUrl, {
        id: reportId
      }).then((result) => {
        this.reports = result.data
        this.$notify({
          title: 'Report active',
          type: 'success',
        })
      })
    },
    save() {
      let $this = this
      Api.post(aoat_config.aoatSaveFormUrl, {
        title: this.title,
        formData: this.formData,
        formSettings: this.formSettings,
        id: this.id
      })
      .then(function (response) {
        if (!$this.id) {
          window.location.href = aoat_config.aoatViewFormUrl + response.data.ID;
        }

        isDirty = false

        $this.$notify({
          title: 'Form saved',
          type: 'success',
        })

        $this.$notify({
          title: 'Don\'t forget to update the report!',
          type: 'warn',
        })
      })
      .catch(function (error) {
        console.log(error);
      });
    },
    downloadJson() {
      let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(this.formData));
      let downloadAnchorNode = document.createElement('a');
      downloadAnchorNode.setAttribute("href",     dataStr);
      downloadAnchorNode.setAttribute("download",  "form_data.json");
      document.body.appendChild(downloadAnchorNode); // required for firefox
      downloadAnchorNode.click();
      downloadAnchorNode.remove();
    },
    importData() {
      try {
        this.formData = JSON.parse(this.importJson);
      } catch (e) {
        alert(e)
      }
    }
  },

  beforeRouteLeave (to, from, next) {
    if(isDirty){
      const answer = window.confirm('It looks like you have been editing something. '
          + 'If you leave before saving, your changes will be lost.')
      if (answer) {
        next()
      } else {
        next(false)
      }
    } else {
      next()
    }
  }
};
</script>

<style>

</style>

