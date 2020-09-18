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
        </tbody>
      </table>
    </div>
</template>

<script>

  export default {

    name: 'TextareaInput',

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
