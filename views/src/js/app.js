import React from 'react'
import ReactDOM from 'react-dom'
import { browserHistory } from 'react-router'
import { syncHistoryWithStore } from 'react-router-redux'
import Root from 'Containers/Root/Root'
import configureStore from './store/configureStore'

// Sass style
import '../resources/sass/style.scss'

// Config axios
import axios from 'axios'
axios.defaults.baseURL = '/api'
axios.defaults.headers.post['Content-Type'] = 'application/json;charset=UTF-8'

// Config moment
import moment from 'moment'
moment.locale('vi')
moment().format('L')

const store = configureStore()
const history = syncHistoryWithStore(browserHistory, store)

const app = document.getElementById('app')

ReactDOM.render(
    <Root store={store} history={history} />,
    app
)
