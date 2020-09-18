<template>
  <div>
        <label for="label">
          {{ object.label }} <template v-if="object.required">*</template>
          <input v-model="value" type="text"  :placeholder="object.placeholder">
<!--          <date-picker v-model="value"  :placeholder="object.placeholder" valueType="format"></date-picker>-->
        </label>

    </div>
</template>

<script>

import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

  export default {

    name: 'TextInput',

    components: {
      DatePicker
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
      };
    },
    computed: {
      value: {
        get () {
          return this.$store.state.assessment[this.object.key]
        },
        set (newValue) {
          return this.$store.dispatch('updateValue', { key: this.object.key, value: newValue})
        }
      }
    },

    methods: {
      updateObject(object) {
        console.log(object);
      }
    }
  };
</script>
