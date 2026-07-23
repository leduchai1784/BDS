const axios = require('axios');
const https = require('https');

const httpsAgent = new https.Agent({ rejectUnauthorized: false });

async function testNksUserProperties() {
  try {
    // 1. Login to NKS
    const loginRes = await axios.post('https://account.nks.vn/api/nks/user/login', {
      username: 'nks.mg0001@gmail.com',
      password: '12345678'
    }, { httpsAgent });

    console.log('Login success:', loginRes.data.success);
    const token = loginRes.data.data?.access_token;
    const nksUser = loginRes.data.data?.user;
    console.log('NKS User info:', nksUser);

    // 2. Test user rsitem endpoints
    const endpoints = [
      'https://account.nks.vn/api/nks/user/rsitem',
      'https://account.nks.vn/api/nks/user/rsitems',
      'https://account.nks.vn/api/nks/user/rsitem/index',
      'https://account.nks.vn/api/nks/user/rsitem/list',
      'https://account.nks.vn/api/nks/user/rsitem/get'
    ];

    for (const ep of endpoints) {
      try {
        const res = await axios.post(ep, { access_token: token }, { httpsAgent });
        console.log(`Endpoint ${ep} SUCCESS:`, JSON.stringify(res.data).substring(0, 300));
      } catch (err) {
        console.log(`Endpoint ${ep} FAILED:`, err.response?.status || err.message);
      }
    }

    // 3. Test public rsitems filtering by NKS User ID/email/phone
    const publicRes = await axios.post('https://online.nks.vn/api/nks/rsitems', {}, { httpsAgent });
    console.log('Public rsitems total count:', publicRes.data.data?.length);
    if (publicRes.data.data) {
      const userItems = publicRes.data.data.filter(item => 
        (nksUser?.id && (item.user_id === nksUser.id || item.sale_id === nksUser.id || item.sale?.id === nksUser.id)) ||
        (nksUser?.email && (item.email === nksUser.email || item.sale?.email === nksUser.email)) ||
        (nksUser?.phone && (item.phone === nksUser.phone || item.sale?.phone === nksUser.phone))
      );
      console.log('Matched user items count in public list:', userItems.length);
      if (userItems.length > 0) {
        console.log('Matched items titles:', userItems.map(i => ({ id: i.id, title: i.title })));
      }
    }
  } catch (err) {
    console.error('Error:', err.response?.data || err.message);
  }
}

testNksUserProperties();
