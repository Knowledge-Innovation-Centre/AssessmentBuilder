<template>
  <div class="aoat-bg-white aoat-rounded aoat-p-6 aoat-container">
    <div class="aoat-flex">
      <div class="aoat-flex-1 aoat-pr-6">
        <div class="aoat-bg-white aoat-grid aoat-grid-cols-2 aoat-gap-10 aoat-mb-5">
          <div>
            <h2 class="aoat-mt-0 aoat-text-gray-700" v-if="reportId">
              Editing report: {{title}} of form: {{ formTitle }}
            </h2>
            <h2 class="aoat-mt-0 aoat-text-gray-700" v-else>
              Creating new report of form: {{ formTitle }}
            </h2>
            <label class="aoat-block aoat-uppercase aoat-tracking-wide aoat-text-gray-700 aoat-text-xs aoat-font-bold aoat-mb-2">
              Title
            </label>
            <input v-model="title"
                   type="text"
                   class="aoat-mb-5 aoat-appearance-none aoat-block aoat-w-full aoat-bg-gray-200 aoat-text-gray-700 aoat-border aoat-border-red-500 aoat-rounded aoat-py-3 aoat-px-4 aoat-mb-3 aoat-leading-tight aoat-focus:outline-none aoat-focus:bg-white">
          </div>
        </div>
        <generic :depth="0" :form="reportData" class="root"></generic>
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
      <div class="aoat-h-full background-grey aoat-top-2 aoat-rounded aoat-sticky aoat-p-4 aoat-max-h-screen aoat-overflow-y-scroll">
        <div class="aoat-text-center aoat-mb-6">
          <button @click="save()"
                  class="aoat-bg-white aoat-py-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
            Save
          </button>
          <router-link :to="'/forms/' + formId">Back to form</router-link>
        </div>
        <h2 class="aoat-mt-0">Builder elements</h2>
        <div>
          <drag
              v-for="(element) in availableBuilderElements"
              :key="element.key"
              class="aoat-bg-white aoat-max-w-xs aoat-py-2 aoat-text-center aoat-mb-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
              :data="element"
              @cut="remove(element)">
            {{element.name}}
          </drag>
        </div>
        <h2 class="aoat-mt-6">Form elements</h2>
        <drag v-for="(element) in availableFormElements"
              :key="element.key"
              class="aoat-max-w-xs aoat-py-2 aoat-text-center aoat-mb-2 aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
              :class="alreadyInReport(element) ? 'background-grey': 'aoat-bg-white'"
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
  import Api from "../Api";
  import formElements from "../utils/form-elements"
  import randomValueHex from "../utils/helpers"
  import Generic from "../components/Generic.vue";
  import {isEmpty} from "lodash";

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
      formTitle: "",
      title: "",
      importJson: "",
      formData: {
        items: []
      },
      form: {},
      formSettings: {
        showPageNumbers: false
      },
      reportData: {},
      report: {},
      reportSettings: {
        showPageNumbers: false
      },
    }
  },
  computed: {
    formId() {
      return this.$route.params.formId
    },
    reportId() {
      return this.$route.params.reportId
    },
    availableFormElements() {
      let usedFormElements = this.getItemsRecursive(this.formData.items)
      return usedFormElements.filter(element => !['column','row','paragraph','legend'].includes(element.type))
    },
    availableBuilderElements() {
      return formElements.filter(element => [
        'column',
        'row',
        'page',
        'paragraph',
        'part_score',
        'total_score',
        'total_score_graph',
        'legend'
      ].includes(element.type))
    },
  },
  mounted() {
  },
  created() {
    this.loadForm()
  },
  watch: {
    reportData: {
      deep: true,
      handler(newData, oldData) {
        this.$store.dispatch('updateReport', this.reportData);

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

      Api.get(aoat_config.aoatGetFormUrl + this.formId).then((result) => {
        this.form  = result.data;
        this.formData  = result.data.form_data[0];
        this.formTitle = this.form.post_title;
        this.formSettings = this.form.form_settings[0];
      })

      Api.get(aoat_config.aoatGetSettingsUrl).then((result) => {
        let settings = {}
        for (let setting of result.data) {
          settings[setting.key] = setting.value
        }
        this.$store.dispatch('updateSettings', settings)
      })

      if (! this.reportId) {
        // let column = this.availableBuilderElements.find(element => element.type === 'column')
        // let row = this.availableBuilderElements.find(element => element.type === 'row')
        // let page = this.availableBuilderElements.find(element => element.type === 'page')
        // row.items.push(column)
        // page.items.push(row)
        this.reportData = {
          key: randomValueHex(10),
          component: "Report",
          reportComponent: "Report",
          name: "Report",
          type: "report",
          items: [
            // page
          ]
        }

        return;
      }
      Api.get(aoat_config.aoatGetReportUrl + this.reportId).then((result) => {
        this.report  = result.data;
        this.reportData  = result.data.report_data[0];
        this.title = this.report.post_title;
        this.reportSettings = this.report.report_settings[0];
      })
    },

    getItemsRecursive(items) {
      for (let item of items) {
        if (item.items) {
          items = items.concat(this.getItemsRecursive(item.items))
        }
      }
      return items;
    },
    remove(n) {
      let index = this.addedElements.indexOf(n);
      this.addedElements.splice(index, 1);
    },
    save() {
      let $this = this
      Api.post(aoat_config.aoatSaveReportUrl, {
        title: this.title,
        reportData: this.reportData,
        reportSettings: this.reportSettings,
        id: this.reportId,
        formId: this.formId
      })
      .then(function (response) {
        if (!$this.reportId) {
          window.location.href = aoat_config.aoatViewReportUrl + $this.formId + '/' + response.data.ID;
        }
        isDirty = false
        $this.$notify({
          title: 'Report saved',
          type: 'success',
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
      downloadAnchorNode.setAttribute("download",  "report_data.json");
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
    },

    alreadyInReport(element) {
      let usedFormElements = this.getItemsRecursive(this.reportData.items)
      return !!usedFormElements.find(usedElement => usedElement.reportItemKey === element.reportItemKey)

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

  root {
    width: 800px;
    padding: 50px;
  }

  .drag {
    border: solid 1px #ccc;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 10px 10px 0 0;
    padding: 10px ;
    font-size: 20px;
    transition: all 0.5s;
  }

  .group {
    display: flex;
  }

  .copy {
    margin: 20px 10px;
    border: 1px solid black;
    height: 100px;
    display: inline-block;
    position: relative;
    flex: 1;
  }

  .cut {
    margin: 20px 10px;
    border: 1px solid black;
    height: 100px;
    display: inline-block;
    position: relative;
    flex: 1;
  }

  .copy::before {
    content: "COPY";
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: rgba(0, 0, 0, 0.4);
    font-size: 25px;
    font-weight: bold;
  }

  .cut::before {
    content: "CUT";
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: rgba(0, 0, 0, 0.4);
    font-size: 25px;
    font-weight: bold;
  }

  .drop-allowed {
    background-color: rgba(0, 255, 0, 0.2);
  }

  .drop-forbidden {
    background-color: rgba(255, 0, 0, 0.2);
  }

  .drop-in {
    box-shadow: 0 0 5px rgba(0, 0, 255, 0.4);
  }

  .list-enter,
  .list-leave-to {
    opacity: 0;
  }

  .list-leave-active {
    position: absolute;
  }

  .elements {
    position: sticky;
    top: 50px;
    z-index: 10;
    background: #f1f1f1;
  }

  .background-grey {
    background: #eee;
  }
</style>

