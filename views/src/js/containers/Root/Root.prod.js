import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { Router } from 'react-router'
import routes from 'Routes'

export default class Root extends Component {
    render() {
        const { store, history } = this.props
        return (
            <Provider store={store} history={history}>
                <div>
                    <Router history={history} routes={routes(store)} />
                </div>
            </Provider>
        )
    }
}
