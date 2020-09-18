<template>
  <div class="home tw-flex">
    <h2 v-if="id">Editing form: {{title}}</h2>
    <h2 v-else>Creating new form</h2>
    <label>
      Enter title here:
      <input v-model="title" type="text">
    </label>
    Short code: [aoat-form id="{{ form.ID }}"]
    <hr>
    <div class="elements">
      <drag v-for="(element) in availableFormElements" :key="element.type" class="drag" :data="element" @cut="remove(element)">
        {{element.name}}
      </drag>
      <div>
        <drag v-for="(element) in availableBuilderElements" :key="element.type" class="drag" :data="element" @cut="remove(element)">
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
      form: {}
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
        this.formData = {
          key: randomValueHex(10),
          component: "Form",
          name: "Form",
          type: "form",
          items: [

          ]
        }
        return;
      }
      axios.get(aoatGetFormUrl + this.id).then((result) => {
        this.form  = result.data;
        this.formData  = result.data.form_data[0];
        this.title = this.form.post_title;
      })
    },
    remove(n) {
      let index = this.addedElements.indexOf(n);
      this.addedElements.splice(index, 1);
    },
    save() {
      let $this = this
      axios.post(aoatSaveFormUrl, {
        title: this.title,
        formData: this.formData,
        id: this.id
      })
      .then(function (response) {
        if (!$this.id) {
          window.location.href = aoatViewFormUrl + response.data.ID;
        }
        console.log(response);
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
    top: 20px;
    z-index: 10;
    background: #f1f1f1;
    padding: 20px 0;
  }
</style>

