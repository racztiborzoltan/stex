# Changelog

## 2.0.0
- rename \Stes\SimpleTemplateXslt -> \Stex\StexXsltProcessor

- \Stex\StexXsltProcessor extends from \XSLTProcessor

- removed methods: 
    - \Stex\StexXsltProcessor->getXsltProcessor()
    - \Stex\StexXsltProcessor->setXsltProcessor()
    - \Stex\StexXsltProcessor->renderToString()
    - \Stex\StexXsltProcessor->renderToDomDocument()
    - \Stex\StexXsltProcessor->transformToDomDocument()
    - \Stex\StexXsltProcessor->transformToString()
    
- overloaded methods: 
    - \Stex\StexXsltProcessor->importStylesheet()
    - \Stex\StexXsltProcessor->transformToDoc()
    - \Stex\StexXsltProcessor->transformToUri()
    - \Stex\StexXsltProcessor->transformToXml()

- changed methods:
    - public methods:
        - \Stex\StexXsltProcessor->render()

- new methods:
    - public methods:
        - \Stex\StexXsltProcessor->__toString()
    - protected methods:
        - \Stex\StexXsltProcessor->_beforeTransform()
        - \Stex\StexXsltProcessor->_afterTransform()
        
- new features:
    - \Stex\StexXsltProcessor
        - PSR-11 ContainerInterface support
        - new small XSLT syntax ('select="this:container(...)') that point always 
          to ContainerInterface object in the current \Stex\StexXsltProcessorTest 
          instance
