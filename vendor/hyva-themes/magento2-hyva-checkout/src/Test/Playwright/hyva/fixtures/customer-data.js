const {pid} = require('process')

const prevRnd = {}
function almostRandom() {
  const range = 10000
  if (Object.keys(prevRnd).length >= range) {
    throw new Error(`Unique number space too small, all ${range} slots have been used`)
  }
  let r
  do {
    r = parseInt(Math.random() * range)
  } while (prevRnd[r])

  prevRnd[r] = true
  return r
}

/* Ensure the email does not conflict with emails generated at the same time in other processes */
export const uniquifyEmail = (email) => {
  const user = email.substring(0, (email.includes('+') ? email.indexOf('+') : email.indexOf('@')))
  const origSuffix = email.substring((email.includes('+') ? email.indexOf('+') + 1 : email.indexOf('@')), email.indexOf('@'))
  const suffix = origSuffix.length ? origSuffix : `${almostRandom()}.${Date.now()}`
  const domain = email.substring(email.indexOf('@') + 1)
  return `${user}+${pid}.${suffix}@${domain}`
}

export const email = `rincewind@uu.test.com`

export const account = {
  email,
  password: 'Test123!'
}

export const addressUS = {
  email,
  firstname: 'Rincewind',
  lastname: 'the Wizard',
  street: 'Unseen University',
  country: 'United States',
  region: 'Michigan', // sample data table rates
  city: 'Ankh Morpork',
  postcode: '48413', //sample data table rates
  telephone: '9999999'
};

export const addressDE = {
  email,
  firstname: 'Twoflower',
  lastname: 'the Tourist',
  street: 'Unknown',
  country: 'Germany',
  region: 'Baden-Württemberg',
  city: 'Agatean Empire',
  postcode: '777777',
  telephone: '3333333'
};
