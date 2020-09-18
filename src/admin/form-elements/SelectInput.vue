<template>
    <div>
      <table class="table">
        <tbody>
        <tr>
          <th>Label/Question:</th>
          <td>
            <input name="label" placeholder="Enter question here" v-model="object.label" type="text" >
          </td>
        </tr>
        <tr>
          <th>Options:</th>
          <td>
            <table>
              <thead>
              <tr>
                <th>Unique ID</th>
                <th>Value</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="option in object.options">
                <td><input type="text" v-model="option.id"></td>
                <td><input type="text" v-model="option.name"></td>
              </tr>
              </tbody>
              <tfoot>
              <tr>
                <td colspan="2" style="text-align: center">
                  <button @click="addOption()">+</button>
                </td>
              </tr>
              </tfoot>
            </table>
          </td>
        </tr>
        </tbody>
      </table>


    </div>
</template>

<script>

  export default {

    name: 'SelectInput',


    components: {
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
        show: false,
      };
    },
    methods: {
      addOption() {
        this.object.options.push({
          id: "",
          name: ""
        })
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
</style>
