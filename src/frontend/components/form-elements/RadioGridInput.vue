<template>
  <div>
    {{ object.label }}
        <table>
          <thead>
          <tr>
            <th></th>
            <th v-for="optionHorizontal in optionsHorizontal" :key="optionHorizontal.id">{{ optionHorizontal.name }}</th>
          </tr>

          </thead>
          <tbody>
          <tr v-for="optionVertical in optionsVertical"  :key="optionVertical.id">
            <td>{{ optionVertical.name }}</td>
            <td v-for="optionHorizontal in optionsHorizontal" :key="optionHorizontal.id">
              <input type="radio" :name="optionVertical.id" v-model="value[optionVertical.id]" :value="optionHorizontal.id"/>
            </td>
          </tr>
          </tbody>
        </table>
    </div>
</template>

<script>

  export default {

    name: 'RadioGridInput',


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
        value: {}
      };
    },
    mounted() {
      for (let optionVertical of this.optionsVertical) {
        this.$set( this.value, optionVertical.id, null)
      }
    },
    computed: {
      optionsHorizontal() {
        return this.object.optionsHorizontal
      },
      optionsVertical() {
        return this.object.optionsVertical
      }
    },
    watch: {
      value() {
        return this.$store.dispatch('updateValue', { key: this.object.key, value: this.value, score: this.score})
      }
    },
    methods: {

    }
  };
</script>
