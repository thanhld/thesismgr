import React, { Component } from 'react'
import { Provider } from 'react-redux'
import { Router } from 'react-router'
import routes from 'Routes'
import { DevTools } from 'Containers'

class Root extends Component {
    render() {
        const { store, history } = this.props
        return (
            <Provider store={store}>
                <div>
                    <Router history={history} routes={routes(store)} />
                    <DevTools />
                </div>
            </Provider>
        )
    }
}

export default Root
