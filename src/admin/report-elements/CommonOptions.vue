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
            <tr>
              <th>Name:</th>
              <td><input v-model="object.name" type="text"></td>
            </tr>
            <tr v-if="typeof object.placeholder !== 'undefined'">
              <th>Placeholder:</th>
              <td><input v-model="object.placeholder" type="text"></td>
            </tr>
            <tr v-if="typeof object.required !== 'undefined'">
              <th>Required:</th>
              <td><input  v-model="object.required" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.hidden !== 'undefined'">
              <th>Hidden:</th>
              <td><input  v-model="object.hidden" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.showTitle !== 'undefined'">
              <th>Show title:</th>
              <td><input  v-model="object.showTitle" type="checkbox"></td>
            </tr>
            <tr v-if="typeof object.includeInAssessmentTitle !== 'undefined'">
              <th>Include in assessment title:</th>
              <td><input v-model="object.includeInAssessmentTitle" type="checkbox"></td>
            </tr>
            <tr>
              <th>Show if:</th>
              <td>
                <select v-model="object.showIf.field">
                  <option v-for="fieldInForm in fieldsInForm" :key="fieldInForm.key" :value="fieldInForm.key">
                    {{fieldInForm.name}}
                  </option>
                </select>
              </td>
              <th v-if="object.showIf.field">is:</th>
              <td v-if="object.showIf.field">
                  <div v-if="isSelect(getFieldByKey(object.showIf.field))">
                    <select v-model="object.showIf.value">
                      <option v-for="option in getFieldByKey(object.showIf.field).options" :key="option.id" :value="option.id">
                        {{option.name}}
                      </option>
                    </select>
                  </div>
                  <div v-else>
                    <input v-model="object.showIf.value" type="text" >
                  </div>
              </td>
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
  import {Multiselect} from "vue-multiselect";

  export default {

    name: 'CommonOptions',

    components: {
      Multiselect
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      fieldsInForm() {
        let childrenKeys = this.getItemsRecursive([this.object]).map(item => item.key)
        return this.getItemsRecursive(this.$store.state.form.items)
                      .filter(field => !childrenKeys.includes(field.key) )
                      .filter(field => ['text','select','date','radio','radio_grid'].includes(field.type))

      }
    },

    data () {
      return {
        show: false,
      };
    },

    methods: {
      getItemsRecursive(items) {
        for (let item of items) {
          if (item.items) {
            items = items.concat(this.getItemsRecursive(item.items))
          }
        }
        return items;
      },
      getFieldByKey(key) {
        return this.fieldsInForm.find(fieldInForm => fieldInForm.key === key)
      },
      isSelect(field) {
        return field.type === 'select'
      },
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
