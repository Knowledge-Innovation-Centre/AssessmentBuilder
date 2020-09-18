<template>
  <div id="vue-frontend-app">
      <generic :form="formData" />
  </div>
</template>

<script>
import axios from "axios";
import Generic from './components/Generic.vue';

export default {
  name: 'App',
  components: {
    Generic
  },
  data() {
    return {
      form: {},
      formData: {}
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      axios.get(aoatGetFormUrl).then((result) => {
        this.form  = result.data;
        this.formData  = result.data.form_data[0];
        console.log(this.formData);
      })
    }
  }
}
</script>

<style>

</style>
