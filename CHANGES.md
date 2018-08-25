- rename \Stes\SimpleTemplateXslt -> \Stex\SimpleTemplateXsltProcessor

- \Stex\SimpleTemplateXsltProcessor extends from \XSLTProcessor

- removed methods: 
    - \Stex\SimpleTemplateXsltProcessor->getXsltProcessor()
    - \Stex\SimpleTemplateXsltProcessor->setXsltProcessor()
    - \Stex\SimpleTemplateXsltProcessor->renderToString()
    - \Stex\SimpleTemplateXsltProcessor->renderToDomDocument()
    - \Stex\SimpleTemplateXsltProcessor->transformToDomDocument()
    - \Stex\SimpleTemplateXsltProcessor->transformToString()
    
- overloaded methods: 
    - \Stex\SimpleTemplateXsltProcessor->importStylesheet()
    - \Stex\SimpleTemplateXsltProcessor->transformToDoc()
    - \Stex\SimpleTemplateXsltProcessor->transformToUri()
    - \Stex\SimpleTemplateXsltProcessor->transformToXml()

- changed methods:
    - public methods:
        - \Stex\SimpleTemplateXsltProcessor->render()

- new methods:
    - public methods:
        - \Stex\SimpleTemplateXsltProcessor->__toString()
    - protected methods:
        - \Stex\SimpleTemplateXsltProcessor->_beforeTransform()
        - \Stex\SimpleTemplateXsltProcessor->_afterTransform()
        
- new classes:
    - \Stex\StexXsltProcessorTest (extends from \Stex\SimpleTemplateXsltProcessor)
        - PSR-11 ContainerInterface support
        - new small XSLT syntax ('select="this:container(...)') that point always 
          to ContainerInterface object in the current \Stex\StexXsltProcessorTest 
          instance

