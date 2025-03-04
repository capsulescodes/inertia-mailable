import React from 'react';
import Table from './tags/Table';
import Header from './Header';
import Footer from './Footer';


interface LayoutProps
{
    logotype ? : string;
    children ? : React.ReactNode;
}

interface SubcopyProps
{
    children ? : React.ReactNode;
}


function Layout( props : LayoutProps ) : React.JSX.Element
{
    const slots = React.Children.toArray( props.children ).filter( child => ! React.isValidElement( child ) || typeof child.type !== 'function' || ( child.type as React.FunctionComponent ).displayName !== 'Subcopy' );

    const subcopies = React.Children.toArray( props.children ).filter( child => React.isValidElement( child ) && typeof child.type === 'function' && ( child.type as React.FunctionComponent ).displayName === 'Subcopy' );


    return (

        <Table className="bg-slate-100" align="center">

            <Header logotype={ props.logotype } />

            <Table className="p-8 drop-shadow-md bg-white" align="center" width="570">

                { slots }

                { subcopies.length && ( <Table className="mt-6 pt-6 border-0 border-t border-solid border-slate-200">{ subcopies }</Table> ) }

            </Table>

            <Footer />

        </Table>
    );
};


function Subcopy( props : SubcopyProps ) : React.JSX.Element
{
    return ( <>{ props.children }</> );
}

Subcopy.displayName = 'Subcopy';


export { Layout, Subcopy };
