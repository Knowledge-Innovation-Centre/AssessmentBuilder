<template>
    <div>
      <div class="more-options-button">
        <button @click="show=!show">
          <span class="dashicons dashicons-arrow-down-alt2"></span>
        </button>
        <button class="remove-button" @click="remove()">
          <span class="dashicons dashicons-trash"></span>
        </button>
      </div>

      <transition>
        <div v-if="show">
          <div class="background-info">
          <table class="table">
            <tbody>
            <tr v-if="typeof object.hidden !== 'undefined'">
              <th>Hidden:</th>
              <td><input  v-model="object.hidden" type="checkbox"></td>
            </tr>
            <tr>
              <th>Classes:</th>
              <td><input v-model="object.class" type="text"></td>
            </tr>
            </tbody>
          </table>
          </div>
        <hr>
        </div>
      </transition>
    </div>
</template>

<script>

  export default {

    name: 'CommonOptionsReport',

    components: {
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
    },

    data () {
      return {
        show: false,
      };
    },

    methods: {
      remove() {
        this.$store.dispatch('removeField', this.object.key);
      }
    }
  };
</script>
<style scoped>

  .table > tbody > tr > th, .table > tbody > tr > td {
    padding: 5px 10px;
  }
  .table > tbody > tr > th {
    text-align: right;
  }
  .background-info {
    background: lightskyblue;
    padding: 5px 10px;
  }
</style>
