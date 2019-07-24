<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">

             <label>Discount Code</label>
             <input type="text" name="code" v-model="code">
             <label>Price Rule</label>
             <select name="price_rule" v-model="price_rule">
                 <option v-for="rule in price_rules" :value="rule.price_rule_id">{{rule.title}}  ({{rule.price_rule_id}})</option>
             </select>

             <div class="button-container"><button class="btn btn-primary" v-on:click="addDiscountCode">Add</button></div>

            </div>

            <div class="col-md-6">
                  <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Code</th>
                        </tr>
                      </thead>
                        <tbody>
                            <tr v-for="discount in discount_codes">
                                <td>{{discount.code}}</td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                price_rules: [],
                discount_codes: [],
                code: '',
                price_rule: '',
            }
        },
        methods:
        {
            addDiscountCode: function()
            {
                let self = this;
                axios.post('/api/discounts',
                {
                    price_rule: self.price_rule,
                    discount_code: self.code
                })
                  .then(function (response) {
                    // handle success
                    Swal.fire({
                      type: 'success',
                      title: 'Discount Code',
                      text: 'Discount Code Added',
                    });
                  })
                  .catch(function (error) {
                    // handle error
                    Swal.fire({
                      type: 'error',
                      title: 'Discount Code',
                      text: 'Error Adding Code',
                    });
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            getPriceRules: function()
            {
                    let self = this;
                    axios.get('/api/pricerules')
                  .then(function (response) {
                    self.price_rules = response.data;
                    console.log('PRICE RULES');
                    console.log(response.data);
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            },
            getDiscount: function()
            {
                 let self = this;
                axios.get('/api/discounts')
                  .then(function (response) {
                    self.discount_codes = response.data;
                    console.log('PRICE RULES');
                    console.log(response.data);
                  })
                  .catch(function (error) {
                    // handle error
                    console.log(error);
                  })
                  .finally(function () {
                    // always executed
                  });
            }
        },
        mounted()
        {
            
            this.getPriceRules();

            this.getDiscount();
            

        }
    }
</script>

<style>
label{
    display: inline-block;
    width: 100%;
    margin-top: 20px;
}

input, select
{
    width: 100%;
}

.button-container
{
    margin-top: 30px;
}
</style>
