import React from 'react';
import Table from './tags/Table';
import Header from './Header';
import Footer from './Footer';


function Layout( { logotype, children } )
{
    const slots = React.Children.toArray( children ).filter( child => ! React.isValidElement( child ) || typeof child.type !== 'function' || child.type.displayName !== 'Subcopy' );

    const subcopies = React.Children.toArray( children ).filter( child => React.isValidElement( child ) && typeof child.type === 'function' && child.type.displayName === 'Subcopy' );


    return (

        <Table className="bg-slate-100" align="center">

            <Header logotype={ logotype } />

            <Table className="p-8 drop-shadow-md bg-white" align="center" width="570">

                { slots }

                { subcopies.length && ( <Table className="mt-6 pt-6 border-0 border-t border-solid border-slate-200">{ subcopies }</Table> ) }

            </Table>

            <Footer />

        </Table>
    );
};


function Subcopy( { children } )
{
    return ( <>{ children }</> );
}

Subcopy.displayName = 'Subcopy';


export { Layout, Subcopy };
