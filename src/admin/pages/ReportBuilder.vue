<template>
  <div class="home">
    <h2 v-if="reportId">Editing report: {{title}} of form: {{ formTitle }}</h2>
    <h2 v-else>Creating new report of form: {{ formTitle }}</h2>
    <div class="aoat-flex">
      <label>
        Enter title here:
        <input v-model="title" type="text">
      </label>
    </div>
    <hr>
    <div class="elements aoat-pb-5">
      <h3 class="aoat-mb-0">Form elements</h3>
      <drag v-for="(element) in availableFormElements" :key="element.key" class="drag" :data="element" @cut="remove(element)">
        {{element.name }}
      </drag>
      <h3>Builder elements</h3>
      <div>
        <drag v-for="(element) in availableBuilderElements" :key="element.key" class="drag" :data="element" @cut="remove(element)">
        {{element.name}}
        </drag>
      </div>
    </div>
    <generic :form="reportData" class="root"></generic>
    <button @click="save()">Save</button>
  </div>
</template>

<script>
  import { Drag } from "vue-easy-dnd";
  import axios from "axios";
  import formElements from "../utils/form-elements"
  import randomValueHex from "../utils/helpers"
  import Generic from "../components/Generic.vue";

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
      usedFormElements = usedFormElements.filter(element => !['column','row','page','paragraph'].includes(element.type))
      usedFormElements = usedFormElements.map(element => {
        if (element.component === 'SelectInput') {
          element.component = 'SelectReportElement'
        } else if (element.component === 'RadioGridInput') {
          element.component = 'RadioGridReportElement'
        }  else if (element.component === 'TextInput') {
          element.component = 'TextReportElement'
        }  else if (element.component === 'TextareaInput') {
          element.component = 'TextareaReportElement'
        }  else if (element.component === 'DateInput') {
          element.component = 'DateReportElement'
        } else if (element.component === 'RadioInput') {
          element.component = 'RadioReportElement'
        } else if (element.component === 'FileUpload') {
          element.component = 'FileUploadReportElement'
        } else {
          element.component = 'DefaultReportElement'
        }
          return element;
        }
      )
      return usedFormElements.filter(element => !['column','row','page','paragraph'].includes(element.type))
    },
    availableBuilderElements() {
      return formElements.filter(element => ['column','row','page','paragraph'].includes(element.type))
    }
  },
  mounted() {
  },
  created() {
    this.loadForm()
  },
  watch: {
    reportData: {
      deep: true,
      handler() {
        this.$store.dispatch('updateReport', this.reportData);
      }
    },
    id() {
      this.loadForm()
    }
  },
  methods: {
    loadForm() {

      axios.get(aoat_config.aoatGetFormUrl + this.formId).then((result) => {
        this.form  = result.data;
        this.formData  = result.data.form_data[0];
        this.formTitle = this.form.post_title;
        this.formSettings = this.form.form_settings[0];
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
          name: "Report",
          type: "report",
          items: [
            // page
          ]
        }

        return;
      }
      axios.get(aoat_config.aoatGetReportUrl + this.reportId).then((result) => {
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
      axios.post(aoat_config.aoatSaveReportUrl, {
        title: this.title,
        reportData: this.reportData,
        reportSettings: this.reportSettings,
        id: this.reportId,
        formId: this.formId
      })
      .then(function (response) {
        if (!$this.reportId) {
          window.location.href = aoatViewReportUrl + $this.formId + '/' + response.data.ID;
        }
      })
      .catch(function (error) {
        console.log(error);
      });
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
</style>

