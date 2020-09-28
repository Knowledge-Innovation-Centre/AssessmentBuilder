<template>
  <div class="home">
    <h2 v-if="id">Editing form: {{title}}</h2>
    <h2 v-else>Creating new form</h2>
    <div class="aoat-flex">
      <label class="aoat-flex-1">
        Enter title here:
        <input v-model="title" type="text">
      </label>
      <div  class="aoat-flex-1">
        Short code: [aoat-form id="{{ form.ID }}"]
      </div>
    </div>
    <hr>
    <template  v-if="id">
      <h2>Reports</h2>
      <template v-for="report in reports">
        <router-link :key="report.id" :to="'/reports/' + id + '/' + report.ID">{{ report.post_title }}</router-link><br>
      </template>
      <router-link  :to="'/reports/' + id + '/create'">Create new</router-link>
      <hr>
    </template>
    <h2>Form settings</h2>
    <table class="table">
      <tbody>
      <tr>
        <th>Show page numbers:</th>
        <td><input v-model="formSettings.showPageNumbers" type="checkbox"></td>
      </tr>
      </tbody>
    </table>
    <hr>
    <div class="elements aoat-pb-5">
      <h3 class="aoat-mb-0">Form elements</h3>
      <drag v-for="(element) in availableFormElements" :key="element.key" class="drag" :data="element" @cut="remove(element)">
        {{element.name}}
      </drag>
      <h3>Builder elements</h3>
      <div>
        <drag v-for="(element) in availableBuilderElements" :key="element.key" class="drag" :data="element" @cut="remove(element)">
        {{element.name}}
        </drag>
      </div>
    </div>
    <generic :form="formData" class="root"></generic>
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
      title: "",
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
      return formElements.filter(element => !['column','row','page','paragraph'].includes(element.type))
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
    formData: {
      deep: true,
      handler() {
        this.$store.dispatch('updateForm', this.formData);
      }
    },
    id() {
      this.loadForm()
    }
  },
  methods: {
    loadForm() {
      if (! this.id) {
        // let column = this.availableBuilderElements.find(element => element.type === 'column')
        // let row = this.availableBuilderElements.find(element => element.type === 'row')
        // let page = this.availableBuilderElements.find(element => element.type === 'page')
        // row.items.push(column)
        // page.items.push(row)
        this.formData = {
          key: randomValueHex(10),
          component: "Form",
          name: "Form",
          type: "form",
          items: [
            // page
          ]
        }
        return;
      }
      axios.get(aoat_config.aoatGetFormUrl + this.id).then((result) => {
        this.form  = result.data;
        this.formData  = this.form.form_data[0];
        this.title = this.form.post_title;
        this.formSettings = this.form.form_settings[0];
        this.reports = this.form.reports
      })
    },
    remove(n) {
      let index = this.addedElements.indexOf(n);
      this.addedElements.splice(index, 1);
    },
    save() {
      let $this = this
      axios.post(aoat_config.aoatSaveFormUrl, {
        title: this.title,
        formData: this.formData,
        formSettings: this.formSettings,
        id: this.id
      })
      .then(function (response) {
        if (!$this.id) {
          window.location.href = aoat_config.aoatViewFormUrl + response.data.ID;
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

  .elements {
    position: sticky;
    top: 50px;
    z-index: 10;
    background: #f1f1f1;
  }
</style>

