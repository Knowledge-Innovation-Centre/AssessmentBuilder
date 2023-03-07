<template>
  <div class="aoat-bg-white aoat-rounded aoat-p-6 aoat-container">
    <div class="aoat-flex">
      <div class="aoat-pr-6 aoat-flex-1">
        <div
          class="aoat-bg-white aoat-grid aoat-grid-cols-3 aoat-gap-10 aoat-mb-5"
        >
          <div>
            <h2 v-if="id" class="aoat-mt-0 aoat-text-gray-700">
              Editing form: {{ title }}
            </h2>
            <h2 v-else class="aoat-mt-0 aoat-text-gray-700">
              Creating new form
            </h2>
            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Title
            </label>
            <input
              v-model="title"
              class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white"
              type="text"
            />
            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Short title
            </label>
            <input
              v-model="formSettings.shortTitle"
              class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white"
              type="text"
            />
            <label
              class="aoat-mb-5  aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold"
            >
              <input v-model="formSettings.showPageNumbers" type="checkbox" />
              Show page numbers
            </label>

            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Select page where form shortcode is inserted
            </label>
            <multiselect
              v-model="formSettings.pageForm"
              :multiple="false"
              :options="pages"
              class="aoat-w-full"
              label="post_title"
              placeholder="Select one"
              track-by="ID"
            />
            <small>Used for updating the assessment</small>
            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Select page where assessment list shortcode is inserted
            </label>
            <multiselect
              v-model="formSettings.pageAssessmentList"
              :multiple="false"
              :options="pages"
              class="aoat-w-full"
              label="post_title"
              placeholder="Select one"
              track-by="ID"
            />
            <small
              >After submitting the form the user will be redirected to this
              page (if catalogue page is not set)</small
            >
            <label
              class="aoat-mb-5  aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              <input
                v-model="formSettings.showAssessmentListLink"
                type="checkbox"
              />
              Show assessment list link
            </label>
            <label
              class="aoat-mb-5  aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              <input v-model="formSettings.hidePDFButton" type="checkbox" />
              Hide PDF button </label
            ><label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Progress bar colour (HEX format)
            </label>
            <input
              v-model="formSettings.progressBarColour"
              class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white"
              type="text"
            />
          </div>
          <div>
            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Select page of catalogue items
            </label>
            <multiselect
              v-model="formSettings.pageCatalog"
              :multiple="false"
              :options="pages"
              class="aoat-w-full"
              label="post_title"
              placeholder="Select one"
              track-by="ID"
            />
            <small
              >After submitting the form the user will be redirected to this
              page</small
            >

            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Select additional forms
            </label>
            <multiselect
              v-model="formSettings.additionalForms"
              :multiple="true"
              :options="additionalForms"
              class="aoat-w-full"
              label="post_title"
              placeholder="Select one"
              track-by="ID"
            />
            <small
              >Fields from this forms will be available in condition
              fields</small
            >
            <label
              class="aoat-mb-5 aoat-mt-5  aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold"
            >
              <input v-model="formSettings.enableExcelExport" type="checkbox" />
              Enable excel export
            </label>
            <label
              class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mt-4"
            >
              Export sort
            </label>
            <input
              v-model="formSettings.exportSort"
              class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white"
              type="number"
            />
          </div>
          <div>
            <template v-if="id">
              <h2
                class="aoat-mt-0 aoat-text-gray-700 aoat-flex aoat-flex-row aoat-justify-between"
              >
                Reports
                <router-link
                  :to="'/reports/' + id + '/create'"
                  class="aoat-no-underline aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
                >
                  +
                </router-link>
              </h2>
              <div
                v-for="report in reports"
                :key="report.ID"
                class="aoat-flex aoat-flex-row aoat-justify-between"
              >
                <router-link
                  :to="'/reports/' + id + '/' + report.ID"
                  class="aoat-text-gray-800 aoat-font-semibold"
                >
                  {{ report.post_title }}
                </router-link>
                <div>
                  <div>
                    <router-link :to="'/reports/' + id + '/' + report.ID">
                      <button
                        class="aoat-bg-white aoat-cursor-pointer aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
                      >
                        <span class="dashicons dashicons-visibility" />
                      </button>
                    </router-link>
                    <button
                      :class="
                        report.post_status === 'publish'
                          ? 'aoat-bg-green-200'
                          : 'aoat-bg-white'
                      "
                      class="aoat-hover:bg-gray-100 aoat-cursor-pointer aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
                      @click="activateReport(report.ID)"
                    >
                      <span class="dashicons dashicons-saved" />
                    </button>
                    <button
                      class="aoat-bg-white aoat-hover:bg-gray-100 aoat-cursor-pointer aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
                      @click="duplicateReport(report.ID)"
                    >
                      <span class="dashicons dashicons-admin-page" />
                    </button>
                    <button
                      class="aoat-bg-white aoat-hover:bg-gray-100 aoat-cursor-pointer aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
                      @click="removeReport(report.ID)"
                    >
                      <span class="dashicons dashicons-trash" />
                    </button>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
        <div class="aoat-text-center aoat-my-3">
          <code>[aoat-form id="{{ form.ID }}"]</code>
          <code>[aoat-form filter-for-export="true" id="{{ form.ID }}"]</code>
          <code>[aoat-assessment-list id="{{ form.ID }}"]</code>
        </div>

        <loader :loading="loading" />
        <generic v-if="!loading" :depth="0" :form="formData" class="root" />
        <div class="aoat-text-center aoat-mt-5">
          <button
            class="aoat-p-3 aoat-text-white aoat-text-lg aoat-border-0 aoat-text-center aoat-cursor-pointer aoat-bg-blue-700"
            @click="save()"
          >
            Save
          </button>

          <button
            class="aoat-p-3 aoat-text-white aoat-text-lg aoat-border-0 aoat-text-center aoat-cursor-pointer aoat-bg-blue-700"
            @click="downloadJson()"
          >
            Export
          </button>
          <tippy
            ref="import_json"
            :interactive="true"
            :max-width="800"
            arrow
            class="aoat-inline-block"
            placement="left"
            theme="light"
            trigger="click"
          >
            <template v-slot:trigger>
              <button
                class="aoat-p-3 aoat-text-white aoat-text-lg aoat-border-0 aoat-text-center aoat-cursor-pointer aoat-bg-blue-700"
              >
                Import...
              </button>
            </template>
            <textarea v-model="importJson" cols="30" rows="10" />
            <button class="aoat-mt-2" @click="importData()">Import</button>
          </tippy>
        </div>
      </div>
      <div
        class="aoat-sidebar aoat-bg-blue-300 aoat-top-2 aoat-sticky aoat-overflow-y-scroll"
      >
        <div class="aoat-text-center">
          <button
            class="aoat-w-full aoat-py-4 aoat-text-white aoat-text-lg aoat-border-0 aoat-text-center aoat-cursor-pointer aoat-bg-blue-700"
            @click="save()"
          >
            Save
          </button>
        </div>
        <div class="aoat-p-4">
          <h2 class="aoat-uppercase aoat-text-blue-700 aoat-text-xs">
            Builder elements
          </h2>
          <div>
            <drag
              v-for="element in availableBuilderElements"
              :key="element.key"
              :data="element"
              class="aoat-bg-blue-700 aoat-p-2 aoat-rounded-md aoat-mb-2 aoat-text-white"
              @cut="remove(element)"
            >
              {{ element.name }}
            </drag>
          </div>
          <h2 class="aoat-mt-6 aoat-uppercase aoat-text-blue-700 aoat-text-xs">
            Form elements
          </h2>
          <drag
            v-for="element in availableFormElements"
            :key="element.key"
            :data="element"
            class="aoat-bg-blue-700 aoat-p-2 aoat-rounded-md aoat-mb-2 aoat-text-white"
            @cut="remove(element)"
          >
            {{ element.name }}
          </drag>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Drag } from "vue-easy-dnd";
import { isEmpty } from "lodash";
import Api from "../Api";
import formElements from "../utils/form-elements";
import randomValueHex from "../utils/helpers";
import Generic from "../components/Generic.vue";
import Loader from "../components/Loader.vue";
import { Multiselect } from "vue-multiselect";

let isDirty = false;

window.onload = function() {
  window.addEventListener("beforeunload", function(e) {
    if (!isDirty) {
      return undefined;
    }

    let confirmationMessage =
      "It looks like you have been editing something. " +
      "If you leave before saving, your changes will be lost.";

    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
  });
};
export default {
  name: "Home",
  components: {
    Drag,
    Generic,
    Loader,
    Multiselect
  },
  data() {
    return {
      loading: false,
      addedElements: [],
      pages: [],
      additionalForms: [],
      title: "",
      importJson: "",
      formData: {},
      form: {},
      reports: [],
      formSettings: {
        shortTitle: null,
        showPageNumbers: false,
        pageForm: null,
        pageAssessmentList: null,
        showAssessmentListLink: false,
        pageCatalog: null,
        additionalForms: [],
        hidePDFButton: false,
        enableExcelExport: false,
        exportSort: 0,
        progressBarColour: 0
      }
    };
  },
  computed: {
    id() {
      return this.$route.params.id;
    },
    availableFormElements() {
      let filteredFormElements = formElements.filter(
        element =>
          ![
            "part_score",
            "total_score",
            "total_score_graph",
            "compare_score"
          ].includes(element.type)
      );
      return filteredFormElements.filter(
        element =>
          !["column", "row", "page", "paragraph"].includes(element.type)
      );
    },
    availableBuilderElements() {
      let filteredFormElements = formElements.filter(
        element =>
          ![
            "part_score",
            "total_score",
            "total_score_graph",
            "compare_score"
          ].includes(element.type)
      );
      return filteredFormElements.filter(element =>
        ["column", "row", "page", "paragraph"].includes(element.type)
      );
    }
  },
  watch: {
    formData: {
      deep: true,
      handler(newData, oldData) {
        this.$store.dispatch("updateForm", this.formData);

        this.$store.dispatch("updateReport", {});

        if (!isEmpty(oldData)) {
          isDirty = true;
        }
      }
    },
    formSettings: {
      deep: true,
      handler() {
        this.$store.dispatch("updateFormSettings", this.formSettings);
      }
    },
    id() {
      this.loadForm();
    }
  },
  mounted() {},
  created() {
    this.loadForm();
  },
  methods: {
    loadForm() {
      this.loading = true;
      Api.get(aoat_config.aoatGetSettingsUrl).then(result => {
        let settings = {};
        for (let setting of result.data) {
          settings[setting.key] = setting.value;
        }
        this.$store.dispatch("updateSettings", settings);
      });
      if (!this.id) {
        this.formData = {
          key: randomValueHex(10),
          component: "Form",
          name: "Form",
          type: "form",
          conditions: [],
          items: [
            // page
          ]
        };
        this.loading = false;
        return;
      }
      Api.get(aoat_config.aoatGetFormUrl + this.id).then(result => {
        this.form = result.data;
        this.formData = this.form.form_data;
        this.title = this.form.post_title;
        this.formSettings = this.form.form_settings;
        this.reports = this.form.reports;

        this.loading = false;
      });

      Api.get(aoat_config.aoatGetPagesUrl).then(result => {
        this.pages = result.data;
      });
      Api.get(aoat_config.aoatGetFormsUrl + "?posts_per_page=-1").then(
        result => {
          this.additionalForms = result.data;
        }
      );
    },
    remove(n) {
      let index = this.addedElements.indexOf(n);
      this.addedElements.splice(index, 1);
    },
    removeReport(reportId) {
      Api.delete(aoat_config.aoatDeleteReportUrl + reportId).then(() => {
        this.reports = this.reports.filter(report => report.ID !== reportId);

        this.$notify({
          title: "Report deleted",
          type: "success"
        });
      });
    },
    duplicateReport(reportId) {
      Api.post(aoat_config.aoatDuplicateReportUrl, {
        id: reportId
      }).then(result => {
        this.reports.push(result.data);
        this.$notify({
          title: "Report duplicated",
          type: "success"
        });
      });
    },
    activateReport(reportId) {
      Api.post(aoat_config.aoatActivateReportUrl, {
        id: reportId
      }).then(result => {
        this.reports = result.data;
        this.$notify({
          title: "Report active",
          type: "success"
        });
      });
    },
    save() {
      let $this = this;
      Api.post(aoat_config.aoatSaveFormUrl, {
        title: this.title,
        formData: this.formData,
        formSettings: this.formSettings,
        id: this.id
      })
        .then(function(response) {
          if (!$this.id) {
            window.location.href =
              aoat_config.aoatViewFormUrl + response.data.ID;
          }

          isDirty = false;

          $this.$notify({
            title: "Form saved",
            type: "success"
          });

          $this.$notify({
            title: "Don't forget to update the report!",
            type: "warn"
          });
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    downloadJson() {
      let dataStr =
        "data:text/json;charset=utf-8," +
        encodeURIComponent(JSON.stringify(this.formData));
      let downloadAnchorNode = document.createElement("a");
      downloadAnchorNode.setAttribute("href", dataStr);
      downloadAnchorNode.setAttribute("download", "form_data.json");
      document.body.appendChild(downloadAnchorNode); // required for firefox
      downloadAnchorNode.click();
      downloadAnchorNode.remove();
    },
    importData() {
      try {
        this.formData = JSON.parse(this.importJson);
      } catch (e) {
        alert(e);
      }
    }
  },

  beforeRouteLeave(to, from, next) {
    if (isDirty) {
      const answer = window.confirm(
        "It looks like you have been editing something. " +
          "If you leave before saving, your changes will be lost."
      );
      if (answer) {
        next();
      } else {
        next(false);
      }
    } else {
      next();
    }
  }
};
</script>

<style></style>
