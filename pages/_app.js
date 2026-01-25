import Head from 'next/head'
import Script from 'next/script'
import Layout from '../components/Layout'
import '../styles/globals.css'

export default function App({ Component, pageProps }) {
  return (
    <>
      <Head>
        <meta name="viewport" content="width=device-width,initial-scale=1" />
      </Head>

      {/* Tailwind CDN (online) - config + script loaded before React */}
      <Script id="tailwind-config" strategy="beforeInteractive" dangerouslySetInnerHTML={{ __html: `
        tailwind.config = {
          theme: {
            extend: {
              colors: { primary: '#0b5fff', accent: '#0ea5a4' }
            }
          }
        }
      ` }} />
      <Script src="https://cdn.tailwindcss.com" strategy="beforeInteractive" />

      <Layout>
        <Component {...pageProps} />
      </Layout>
    </>
  )
}
